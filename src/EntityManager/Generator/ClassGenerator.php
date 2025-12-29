<?php

namespace Sarue\Orm\EntityManager\Generator;

use BcMath\Number;
use Laminas\Code\Generator\ClassGenerator as LaminasClassGenerator;
use Laminas\Code\Generator\FileGenerator;
use Laminas\Code\Generator\MethodGenerator;
use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\EntityManager\Generator\Wrapper\EntityTypeDefinitionWrapper;
use Sarue\Orm\EntityManager\Generator\Wrapper\FieldDefinitionWrapper;
use Sarue\Orm\Field\Type\FieldTypeInterface;
use Sarue\Orm\Query\Condition\Group\AndConditionGroupBase;
use Sarue\Orm\Query\Condition\Group\OrConditionGroupBase;
use Sarue\Orm\Query\QueryBase;
use Sarue\Orm\Query\QueryFactoryBase;
use Sarue\Orm\Schema\EntityDiscoveryCacheBase;
use Sarue\Orm\Entity\Type\EntityType;

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
        $entityTypeDefinitionWrappers = $this->discoverEntityDefinitions();
        foreach ($entityTypeDefinitionWrappers as $entityTypeDefinitionWrapper) {
            $queryClasses[] = $this->generateClassesForEntity($entityTypeDefinitionWrapper->entityTypeDefinition);
        }

        $this->generateQueryFactory($queryClasses);
        $this->generateEntityDiscoveryCacheClass($entityTypeDefinitionWrappers);
    }

    /**
     * @return EntityTypeDefinitionWrapper[]
     */
    protected function discoverEntityDefinitions(): array
    {
        $entityTypeDefinitionWrappers = [];
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

            $fieldDefinitionWrappers = $this->discoverFieldDefinitions($classReflection);
            $fieldDefinitions = array_map(fn(FieldDefinitionWrapper $fieldDefinitionWrapper): FieldTypeInterface => $fieldDefinitionWrapper->fieldDefinition, $fieldDefinitionWrappers);

            $entityTypeDefinitionWrappers[$className] = new EntityTypeDefinitionWrapper(
                EntityType::fromValues(
                    $this->getShortClassName($className),
                    $className,
                    $fieldDefinitions,
                ),
                $classReflection,
                $fieldDefinitionWrappers,
            );
        }

        return $entityTypeDefinitionWrappers;
    }

    /**
     * @return FieldDefinitionWrapper[]
     */
    protected function discoverFieldDefinitions(\ReflectionClass $classReflection): array
    {
        $fieldDefinitionWrappers = [];

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
            $fieldDefinition->initializeDefinition($property->getName(), $property->getType()?->__toString());

            $fieldDefinition->validateDefinition();

            $fieldDefinitionWrappers[$fieldDefinition->getFieldName()] = new FieldDefinitionWrapper(
                $fieldDefinition,
                $fieldAttribute,
            );
        }

        return $fieldDefinitionWrappers;
    }

    protected function generateClassesForEntity(EntityType $entityTypeDefinition): string
    {
        $methodParameters = "(\n?\\Sarue\\Orm\\Query\\Condition\\ConditionInterface \$_condition = null,\n";
        $baseMethodCall = "return \$this->addConditions(\$_condition,\n [\n";
        foreach ($entityTypeDefinition->fields as $fieldDefinition) {
            $conditionType = $fieldDefinition->getConditionType();
            $methodParameters .= "?\\{$conditionType} \${$fieldDefinition->getFieldName()} = null,\n";
            $baseMethodCall .= "'{$fieldDefinition->getFieldName()}' => \${$fieldDefinition->getFieldName()},";
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
            namespaceName: $this->generatedNamespace.'Entity\\Query',
            extends: QueryFactoryBase::class,
            methods: $methods,
        ));
    }

    /**
     * @param EntityTypeDefinitionWrapper[] $entityTypeDefinitionWrappers
     */
    protected function generateEntityDiscoveryCacheClass(array $entityTypeDefinitionWrappers): void
    {
        $generatedCode = "return [\n";
        foreach ($entityTypeDefinitionWrappers as $entityTypeDefinitionWrapper) {
            $entityTypeDefinition = $entityTypeDefinitionWrapper->entityTypeDefinition;
            $generatedCode .= '    '.var_export($entityTypeDefinition->className, true).' => \\'.EntityType::class."::fromValues(\n";
            $generatedCode .= '        '.var_export($entityTypeDefinition->name, true).",\n";
            $generatedCode .= '        '.var_export($entityTypeDefinition->className, true).",\n";
            $generatedCode .= "        [\n";
            foreach ($entityTypeDefinitionWrapper->fieldDefinitionWrappers as $fieldDefinitionWrapper) {
                $fieldDefinition = $fieldDefinitionWrapper->fieldDefinition;
                $generatedCode .= "            ".var_export($fieldDefinition->getFieldName(), true).' => new \\'.get_class($fieldDefinition)."(\n";
                foreach ($fieldDefinitionWrapper->attributeReflection->getArguments() as $argumentName => $argumentValue) {
                    $generatedCode .= '                '.$argumentName.': '.$this->safeVarExport($argumentValue).",\n";
                }
                $generatedCode .= "            )->initializeDefinition(".var_export($fieldDefinition->getFieldName(), true).",".var_export($fieldDefinition->getPropertyType(), true)."),\n";
            }
            $generatedCode .= "        ],\n";
            $generatedCode .= "    ),\n";
        }
        $generatedCode .= "];\n";

        $methods = [
            new MethodGenerator(
                name: 'getCachedEntityDefinitions',
                body: $generatedCode,
            )->setReturnType('array'),
        ];
        //$generatedCode .= "return unserialize('".str_replace("'", "\\'", serialize($entityDiscoveryCache))."');\n}\n}";

        $this->dump('/Entity/EntityDiscoveryCache.php', new LaminasClassGenerator(
            name: 'EntityDiscoveryCache',
            namespaceName: $this->generatedNamespace.'Entity',
            extends: EntityDiscoveryCacheBase::class,
            methods: $methods,
        ));
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

    protected function safeVarExport(mixed $variable): string
    {
        if (is_int($variable) || is_string($variable)) {
           return var_export($variable, true);
        }
        elseif ($variable instanceof Number) {
            return "new \\BcMath\\Number('{$variable}')";
        }

        throw new \Exception('Unsupported parameter type');
    }
}
