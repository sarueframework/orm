<?php

namespace Sarue\Orm\EntityManager\Generator;

use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Field\FieldInterface;

class ClassGenerator
{
    public function __construct(
        protected string $entityDirectory,
        protected string $generatedBaseDirectory,
        protected string $entityNamespace = 'App\\Entity\\',
        protected string $generatedNamespace = 'App\\Generated\\Sarue\\',
    ) {
    }

    public function generateClasses(): void
    {
        $queryClasses = [];
        $entityDiscoveryCache = [];
        foreach ($this->discoverEntityClasses() as $entityClass) {
            $entityTypeName = $this->getShortClassName($entityClass);
            $entityDiscoveryCache[$entityTypeName] = [
                'class' => $entityClass,
                'fields' => $this->discoverEntityFields($entityClass),
            ];
            $queryClasses[] = $this->generateQueryClassForEntity($entityClass, $entityDiscoveryCache[$entityTypeName]['fields']);
        }

        $this->generateQueryFactory($queryClasses);
        $this->generateEntityDiscoveryCacheClass($entityDiscoveryCache);
    }

    /**
     * @return string[]
     */
    protected function discoverEntityClasses(): array
    {
        $phpFiles = array_filter(
            \scandir($this->entityDirectory),
            fn ($filename) => str_ends_with($filename, '.php'),
        );

        $possibleClasses = array_map(
            fn ($filename) => $this->entityNamespace.substr($filename, 0, -4),
            $phpFiles,
        );

        return array_filter(
            $possibleClasses,
            fn ($className) => is_subclass_of($className, EntityInterface::class) && !(new \ReflectionClass($className))->isAbstract(),
        );
    }

    protected function discoverEntityFields(string $entityFullClassName): array
    {
        $reflection = new \ReflectionClass($entityFullClassName);
        $fields = [];

        foreach ($reflection->getProperties() as $property) {
            $type = $property->getType()->__toString();
            if (class_exists($type) && is_subclass_of($type, FieldInterface::class)) {
                $this->validateField($property);

                $fields[$property->getName()] = [
                    'type' => $type,
                    'condition_type' => [$type, 'getConditionType'](),
                ];
            }
        }

        return $fields;
    }

    protected function validateField(\ReflectionProperty $property): void
    {
        if ('id' === $property->getName()) {
            // @todo Create Exception classes
            throw new \Exception('A property named ID is forbidden.');
        }
    }

    protected function generateQueryClassForEntity(string $entityFullClassName, array $discoveriedFields): string
    {
        $reflection = new \ReflectionClass($entityFullClassName);

        $entityNameParts = explode('\\', $entityFullClassName);
        $queryClassName = array_pop($entityNameParts).'Query';

        $namespace = $this->generatedNamespace.'Entity\\Query';

        $generatedCode = "<?php\n\nnamespace $namespace;\n\nclass $queryClassName extends \\Sarue\\Orm\\Query\\QueryBase {\n";

        foreach ($discoveriedFields as $fieldName => $fieldData) {
            $generatedCode .= "public function {$fieldName}(\\{$fieldData['condition_type']} \$condition): static { return \$this->addCondition(\$condition); }\n";
        }

        $generatedCode .= "}\n";
        $this->dump('/Entity/Query/'.$queryClassName.'.php', $generatedCode);

        return $namespace.'\\'.$queryClassName;
    }

    protected function generateQueryFactory(array $queryClasses): void
    {
        $namespace = $this->generatedNamespace.'Entity\\Query';
        $generatedCode = "<?php\n\nnamespace $namespace;\n\nclass QueryFactory extends \\Sarue\\Orm\\Query\\QueryFactoryBase {\n";

        foreach ($queryClasses as $queryClass) {
            $className = $this->getShortClassName($queryClass);
            $generatedCode .= "function get$className(): \\$queryClass { return \$this->instantiateQuery(".var_export($queryClass, true)."); }\n";
        }
        $generatedCode .= "}\n";
        $this->dump('/Entity/Query/QueryFactory.php', $generatedCode);
    }

    protected function generateEntityDiscoveryCacheClass(array $entityDiscoveryCache): void
    {
        $namespace = $this->generatedNamespace.'Entity';
        $generatedCode = "<?php\n\nnamespace $namespace;\n\nclass EntityDiscoveryCache {\n";
        $generatedCode .= "function getCachedEntityDefinitions(): array {\n";
        $generatedCode .= 'return '.var_export($entityDiscoveryCache, true).";\n}\n}";

        $this->dump('/Entity/EntityDiscoveryCache.php', $generatedCode);
    }

    protected function getShortClassName(string $fullClassName): string
    {
        return substr($fullClassName, strrpos($fullClassName, '\\') + 1);
    }

    protected function dump($classPath, $generatedCode): void
    {
        file_put_contents($this->generatedBaseDirectory.$classPath, $generatedCode);
    }
}
