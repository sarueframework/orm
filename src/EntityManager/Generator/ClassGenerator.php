<?php

namespace Sarue\Orm\EntityManager\Generator;

use Attribute;
use ReflectionClass;
use ReflectionProperty;
use Sarue\Orm\Attribute\Entity;
use Sarue\Orm\Attribute\Field;
use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Field\FieldInterface;
use Sarue\Orm\Field\Type\Numeric\Integer;
use Sarue\Orm\Field\Type\Text\Text;
use Sarue\Orm\Schema\EntityDefinition;
use Sarue\Orm\Schema\FieldDefinition;

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
        $entityDefinitions = $this->discoverEntityDefinitions();
        foreach ($entityDefinitions as $entityDefinition) {
            $queryClasses[] = $this->generateQueryClassForEntity($entityDefinition);
        }

        $this->generateQueryFactory($queryClasses);
        $this->generateEntityDiscoveryCacheClass($entityDefinitions);
    }

    /**
     * @return \Sarue\Orm\Attribute\Entity[]
     */
    protected function discoverEntityDefinitions(): array
    {
        $entityDefinitions = [];
        foreach (\scandir($this->entityDirectory) as $filename) {
            if (!str_ends_with($filename, '.php')) {
                continue;
            }

            $className = $this->entityNamespace.substr($filename, 0, -4);

            if (!class_exists($className)) {
                continue;
            }

            $classReflection = new \ReflectionClass($className);
            $entityAttributes = $classReflection->getAttributes(Entity::class);

            if (empty($entityAttributes)) {
                continue;
            }

            if (!is_subclass_of($className, EntityInterface::class)) {
                throw new \Exception('Class ' . $className . ' has attribute Entity but it not a descendant of EntityInterface.');
            }

            if ($classReflection->isAbstract()) {
                throw new \Exception('Class ' . $className . ' has attribute Entity but is abstract.');
            }

            $entityDefinitions[] = new EntityDefinition(
                $this->getShortClassName($className),
                $className,
                $this->discoverFieldDefinitions($classReflection),
                ...reset($entityAttributes)->getArguments()
            );
        };

        return $entityDefinitions;
    }

    protected function discoverFieldDefinitions(ReflectionClass $classReflection): array
    {
        $fieldDefinitions = [];

        foreach ($classReflection->getProperties() as $property) {
            $fieldAttributes = $property->getAttributes(Field::class);

            if (empty($fieldAttributes)) {
                continue;
            }

            $fieldAttribute = reset($fieldAttributes);

            $type = $property->getType()->__toString();

            // @todo Add support for other types of columns that map to a scalar property.
            $type = match($type) {
                'int' => Integer::class,
                'string' => Text::class,
                default =>
                    class_exists($type) && is_subclass_of($type, FieldInterface::class) ?
                        $type :
                        throw new \Exception("'$type' is not a valid type for a field."),
            };

            $fieldDefinition = new FieldDefinition(
                $property->getName(),
                $type,
                [$type, 'getConditionType'](),
            );

            $fieldDefinition->validate();

            $fieldDefinitions[$fieldDefinition->name] = $fieldDefinition;
        }

        return $fieldDefinitions;
    }

    protected function generateQueryClassForEntity(EntityDefinition $entityDefinition): string
    {
        $reflection = new \ReflectionClass($entityDefinition->className);

        $entityNameParts = explode('\\', $entityDefinition->className);
        $queryClassName = array_pop($entityNameParts).'Query';

        $namespace = $this->generatedNamespace.'Entity\\Query';

        $generatedCode = "<?php\n\nnamespace $namespace;\n\nclass $queryClassName extends \\Sarue\\Orm\\Query\\QueryBase {\n";

        foreach ($entityDefinition->fields as $fieldDefinition) {
            $generatedCode .= "public function {$fieldDefinition->name}(\\{$fieldDefinition->conditionType} \$condition): static { return \$this->addCondition(\$condition); }\n";
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
