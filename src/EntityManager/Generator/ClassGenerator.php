<?php

namespace Sarue\Orm\EntityManager\Generator;

use Laminas\Code\Generator\ClassGenerator as LaminasClassGenerator;
use Laminas\Code\Generator\FileGenerator;
use Laminas\Code\Generator\MethodGenerator;
use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Field\Type\FieldTypeInterface;
use Sarue\Orm\Query\Condition\Group\AndConditionGroupBase;
use Sarue\Orm\Query\Condition\Group\OrConditionGroupBase;
use Sarue\Orm\Query\QueryBase;
use Sarue\Orm\Query\QueryFactoryBase;
use Sarue\Orm\Schema\EntityType;

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
        $entityTypeDefinitions = $this->discoverEntityDefinitions();
        foreach ($entityTypeDefinitions as $entityTypeDefinition) {
            $queryClasses[] = $this->generateClassesForEntity($entityTypeDefinition);
        }

        $this->generateQueryFactory($queryClasses);
        $this->generateEntityDiscoveryCacheClass($entityTypeDefinitions);
    }

    /**
     * @return EntityType[]
     */
    protected function discoverEntityDefinitions(): array
    {
        $entityTypeDefinitions = [];
        foreach (\scandir($this->entityDirectory) as $filename) {
            if (!str_ends_with($filename, '.php')) {
                continue;
            }

            $className = $this->entityNamespace.substr($filename, 0, -4);

            if (!class_exists($className)) {
                continue;
            }

            $classReflection = new \ReflectionClass($className);
            $entityAttributes = $classReflection->getAttributes(EntityType::class);

            if (empty($entityAttributes)) {
                continue;
            }

            if (!is_subclass_of($className, EntityInterface::class)) {
                throw new \Exception('Class '.$className.' has attribute Entity but it not a descendant of EntityInterface.');
            }

            if ($classReflection->isAbstract()) {
                throw new \Exception('Class '.$className.' has attribute Entity but is abstract.');
            }

            $entityTypeDefinitions[$className] = EntityType::fromValues(
                $this->getShortClassName($className),
                $className,
                $this->discoverFieldDefinitions($classReflection),
            );
        }

        return $entityTypeDefinitions;
    }

    protected function discoverFieldDefinitions(\ReflectionClass $classReflection): array
    {
        $fieldDefinitions = [];

        foreach ($classReflection->getProperties() as $property) {
            $fieldAttributes = $property->getAttributes(FieldTypeInterface::class, \ReflectionAttribute::IS_INSTANCEOF);

            if (empty($fieldAttributes)) {
                continue;
            }

            if (1 !== count($fieldAttributes)) {
                throw new \Exception('Cannot declare more than one field type for a property.');
            }

            $fieldAttribute = reset($fieldAttributes);

            if ('id' === $property->getName()) {
                throw new \Exception('Reserved word "id" cannot be used as a field name');
            }

            /** @var FieldTypeInterface */
            $fieldDefinition = $fieldAttribute->newInstance();
            $fieldDefinition->fieldName = $property->getName();
            $fieldDefinition->propertyType = $property->getType()?->__toString();

            $fieldDefinition->validateDefinition();

            $fieldDefinitions[$fieldDefinition->fieldName] = $fieldDefinition;
        }

        return $fieldDefinitions;
    }

    protected function generateClassesForEntity(EntityType $entityTypeDefinition): string
    {
        $reflection = new \ReflectionClass($entityTypeDefinition->className);

        $methodParameters = "(\n?\\Sarue\\Orm\\Query\\Condition\\ConditionInterface \$_condition = null,\n";
        $baseMethodCall = "return \$this->addConditions(\$_condition,\n [\n";
        foreach ($entityTypeDefinition->fields as $fieldDefinition) {
            $conditionType = $fieldDefinition->getConditionType();
            $methodParameters .= "?\\{$conditionType} \${$fieldDefinition->fieldName} = null,\n";
            $baseMethodCall .= "'{$fieldDefinition->fieldName}' => \${$fieldDefinition->fieldName},";
        }
        $methodParameters .= ')';
        $baseMethodCall .= "\n]);";

        $this->generateSingleClassForEntity($entityTypeDefinition, 'OrConditionGroup', OrConditionGroupBase::class, $methodParameters, $baseMethodCall, [
            'or',
        ]);

        $this->generateSingleClassForEntity($entityTypeDefinition, 'AndConditionGroup', AndConditionGroupBase::class, $methodParameters, $baseMethodCall, [
            'and',
        ]);

        return $this->generateSingleClassForEntity($entityTypeDefinition, 'Query', QueryBase::class, $methodParameters, $baseMethodCall, [
            'where',
            'and',
        ]);
    }

    protected function generateSingleClassForEntity(EntityType $entityTypeDefinition, string $classNameSuffix, string $classBase, string $methodParameters, string $baseMethodCall, array $methodsToGenerate): string
    {
        $namespace = $this->generatedNamespace.'Entity\\Query';
        $shortEntityClassName = $this->getShortClassName($entityTypeDefinition->className);
        $generatedClassName = $shortEntityClassName.$classNameSuffix;

        $generatedCode = "<?php\n\nnamespace $namespace;\n\nclass $generatedClassName extends \\{$classBase} {\n";

        if ('Query' === $classNameSuffix) {
            $generatedCode .= "public const string ENTITY_CLASS = \\{$entityTypeDefinition->className}::class;\n";
            $generatedCode .= "public function orGroup(): {$shortEntityClassName}OrConditionGroup { return new {$shortEntityClassName}OrConditionGroup(); }\n";
            $generatedCode .= "public function andGroup(): {$shortEntityClassName}AndConditionGroup { return new {$shortEntityClassName}AndConditionGroup(); }\n";
            $generatedCode .= "/**\n";
            $generatedCode .= ' * @return \\'.$entityTypeDefinition->className."[]\n";
            $generatedCode .= " */\n";
            $generatedCode .= "public function loadAll(): array { return \$this->doLoadAll(); }\n";
        }

        foreach ($methodsToGenerate as $methodToGenerate) {
            $generatedCode .= "public function {$methodToGenerate}{$methodParameters} : static {\n{$baseMethodCall}\n}\n";
        }

        $generatedCode .= "}\n";

        $this->dump('/Entity/Query/'.$generatedClassName.'.php', $generatedCode);

        return $namespace.'\\'.$generatedClassName;
    }

    protected function generateQueryFactory(array $queryClasses): void
    {
        $namespace = $this->generatedNamespace.'Entity\\Query';
        $methods = [];

        foreach ($queryClasses as $queryClass) {
            $className = $this->getShortClassName($queryClass);
            $methods[] = new MethodGenerator(
                name: 'get'.$className,
                body: "return \$this->instantiateQuery(".var_export($queryClass, true).");",
            )->setReturnType($queryClass);
        }

        $this->dump('/Entity/Query/QueryFactory.php', new LaminasClassGenerator(
            name: 'QueryFactory',
            namespaceName: $namespace,
            extends: QueryFactoryBase::class,
            methods: $methods,
        ));
    }

    protected function generateEntityDiscoveryCacheClass(array $entityDiscoveryCache): void
    {
        $namespace = $this->generatedNamespace.'Entity';
        $generatedCode = "<?php\n\nnamespace $namespace;\n\nclass EntityDiscoveryCache implements \\Sarue\\Orm\\Schema\\EntityDiscoveryCacheInterface {\n";
        $generatedCode .= "public function getCachedEntityDefinitions(): array {\n";
        $generatedCode .= "return unserialize('".str_replace("'", "\\'", serialize($entityDiscoveryCache))."');\n}\n}";

        $this->dump('/Entity/EntityDiscoveryCache.php', $generatedCode);
    }

    protected function getShortClassName(string $fullClassName): string
    {
        return substr($fullClassName, strrpos($fullClassName, '\\') + 1);
    }

    protected function dump($classPath, LaminasClassGenerator|string $classGenerator): void
    {
        if (is_string($classGenerator)) {
            file_put_contents($this->generatedBaseDirectory.$classPath, $classGenerator);
            return;
        }

        file_put_contents($this->generatedBaseDirectory.$classPath, new FileGenerator([
            'classes' => [$classGenerator],
        ])->generate());
    }
}
