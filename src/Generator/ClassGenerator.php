<?php

namespace Sarue\Orm\Generator;

use BcMath\Number;
use Laminas\Code\Generator\ClassGenerator as LaminasClassGenerator;
use Laminas\Code\Generator\DocBlock\Tag\ReturnTag;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\FileGenerator;
use Laminas\Code\Generator\MethodGenerator;
use Laminas\Code\Generator\ParameterGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use Laminas\Code\Generator\TypeGenerator;
use Laminas\Code\Generator\ValueGenerator;
use Sarue\Orm\Entity\AbstractBaseEntity;
use Sarue\Orm\Entity\Type\AbstractEntityTypeDefinitionRepository;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Exception\EntityDefinition\AbstractEntityTypeException;
use Sarue\Orm\Exception\EntityDefinition\BadInheritanceException;
use Sarue\Orm\Exception\EntityDefinition\MultipleAttributesInPropertyException;
use Sarue\Orm\Field\Type\FieldTypeInterface;
use Sarue\Orm\Generator\Laminas\MethodTagWithParameters;
use Sarue\Orm\Generator\Laminas\PropertyGeneratorWithTypedConstant;
use Sarue\Orm\Generator\Wrapper\EntityTypeDefinitionWrapper;
use Sarue\Orm\Generator\Wrapper\FieldDefinitionWrapper;
use Sarue\Orm\Query\AbstractQuery;
use Sarue\Orm\Query\AbstractQueryFactory;
use Sarue\Orm\Query\Condition\ConditionInterface;
use Sarue\Orm\Query\Condition\Group\AbstractAndConditionGroup;
use Sarue\Orm\Query\Condition\Group\AbstractOrConditionGroup;

class ClassGenerator
{
    public const GENERATED_CLASS_NAMESPACE = 'Sarue\\Orm\\Generated\\';

    public function __construct(
        protected string $entityDirectory,
        protected string $generatedBaseDirectory,
        protected string $entityNamespace = 'App\\Entity\\',
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
        $this->generateEntityTypeDefinitionRepositoryClass($entityTypeDefinitionWrappers);
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

            if (!is_subclass_of($className, AbstractBaseEntity::class)) {
                throw new BadInheritanceException('Class '.$className.' has attribute #[EntityType] but it not a descendant of Sarue\Orm\Entity\AbstractBaseEntity.');
            }

            if ($classReflection->isAbstract()) {
                throw new AbstractEntityTypeException('Class '.$className.' has attribute #[EntityType] but is abstract.');
            }

            $fieldDefinitionWrappers = $this->discoverFieldDefinitions($classReflection);
            $fieldDefinitions = array_map(fn (FieldDefinitionWrapper $fieldDefinitionWrapper): FieldTypeInterface => $fieldDefinitionWrapper->fieldDefinition, $fieldDefinitionWrappers);

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
                throw new MultipleAttributesInPropertyException('Multiple field types have been declared for property "'.$property->getName().'" in '.$classReflection->getName().'.');
            }

            $fieldAttribute = reset($fieldAttributes);

            /** @var FieldTypeInterface */
            $fieldDefinition = $fieldAttribute->newInstance();
            $fieldDefinition->initializeDefinition($property->getName(), $property->getType()?->__toString());

            $fieldDefinition->validateDefinition();

            $fieldDefinitionWrappers[$fieldDefinition->getFieldName()] = new FieldDefinitionWrapper(
                $fieldDefinition,
                $property->getType()?->__toString(),
                $fieldAttribute->getArguments(),
            );
        }

        return $fieldDefinitionWrappers;
    }

    protected function generateClassesForEntity(EntityType $entityTypeDefinition): string
    {
        $methodParameters = [
            new ParameterGenerator('_condition', '?'.ConditionInterface::class, new ValueGenerator(type: ValueGenerator::TYPE_NULL)),
        ];

        $this->generateSingleClassForEntity($entityTypeDefinition, 'OrConditionGroup', AbstractOrConditionGroup::class, $methodParameters, [
            'or',
        ]);

        $this->generateSingleClassForEntity($entityTypeDefinition, 'AndConditionGroup', AbstractAndConditionGroup::class, $methodParameters, [
            'and',
        ]);

        return $this->generateSingleClassForEntity($entityTypeDefinition, 'Query', AbstractQuery::class, $methodParameters, [
            'where',
            'and',
        ]);
    }

    protected function generateSingleClassForEntity(EntityType $entityTypeDefinition, string $classNameSuffix, string $classBase, array $methodParameters, array $methodsToGenerate): string
    {
        $namespace = static::GENERATED_CLASS_NAMESPACE.'Entity\\Query';
        $shortEntityClassName = $this->getShortClassName($entityTypeDefinition->className);
        $generatedClassName = $shortEntityClassName.$classNameSuffix;
        $methods = [];

        if ('Query' === $classNameSuffix) {
            $methods[] = new MethodGenerator(
                name: 'orGroup',
                body: "return new {$shortEntityClassName}OrConditionGroup();",
            )->setReturnType($namespace.'\\'.$shortEntityClassName.'OrConditionGroup');

            $methods[] = new MethodGenerator(
                name: 'andGroup',
                body: "return new {$shortEntityClassName}AndConditionGroup();",
            )->setReturnType($namespace.'\\'.$shortEntityClassName.'AndConditionGroup');

            $methods[] = new MethodGenerator(
                name: 'loadById',
                parameters: [
                    new ParameterGenerator('id', 'string'),
                ],
                body: 'return $this->doLoadById($id);',
            )->setReturnType($entityTypeDefinition->className);

            $methods[] = new MethodGenerator(
                name: 'loadAll',
                body: 'return $this->doLoadAll();',
            )
                ->setDocBlock(new DocBlockGenerator()
                    ->setTag(new ReturnTag([
                        'datatype' => '\\'.$entityTypeDefinition->className.'[]',
                    ])),
                )
                ->setReturnType('array');
        }

        $classDocBlock = new DocBlockGenerator('Query for '.$shortEntityClassName.' entities.');
        foreach ($methodsToGenerate as $methodToGenerate) {
            $classDocBlock->setTag(
                new MethodTagWithParameters($methodToGenerate, ['static'])
                    ->setParameters($methodParameters)
            );
        }

        $classGenerator = new LaminasClassGenerator(
            name: $generatedClassName,
            namespaceName: static::GENERATED_CLASS_NAMESPACE.'Entity\\Query',
            extends: $classBase,
            methods: $methods,
            docBlock: $classDocBlock,
        )
            ->addConstantFromGenerator(
                new PropertyGeneratorWithTypedConstant(
                    'ENTITY_CLASS',
                    $entityTypeDefinition->className,
                    PropertyGenerator::FLAG_CONSTANT,
                    TypeGenerator::fromTypeString('string'),
                )
            );
        $this->dump('/Entity/Query/'.$generatedClassName.'.php', $classGenerator);

        return $namespace.'\\'.$generatedClassName;
    }

    protected function generateQueryFactory(array $queryClasses): void
    {
        $methods = [];
        foreach ($queryClasses as $queryClass) {
            $className = $this->getShortClassName($queryClass);
            $methods[] = new MethodGenerator(
                name: 'get'.$className,
                body: 'return $this->instantiateQuery('.var_export($queryClass, true).');',
            )->setReturnType($queryClass);
        }

        $this->dump('/Entity/Query/QueryFactory.php', new LaminasClassGenerator(
            name: 'QueryFactory',
            namespaceName: static::GENERATED_CLASS_NAMESPACE.'Entity\\Query',
            extends: AbstractQueryFactory::class,
            methods: $methods,
        ));
    }

    /**
     * @param EntityTypeDefinitionWrapper[] $entityTypeDefinitionWrappers
     */
    protected function generateEntityTypeDefinitionRepositoryClass(array $entityTypeDefinitionWrappers): void
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
                $generatedCode .= '            '.var_export($fieldDefinition->getFieldName(), true).' => new \\'.get_class($fieldDefinition)."(\n";
                foreach ($fieldDefinitionWrapper->arguments as $argumentName => $argumentValue) {
                    $generatedCode .= '                '.$argumentName.': '.$this->safeVarExport($argumentValue).",\n";
                }
                $generatedCode .= '            )->initializeDefinition('.var_export($fieldDefinition->getFieldName(), true).','.var_export($fieldDefinitionWrapper->propertyType, true)."),\n";
            }
            $generatedCode .= "        ],\n";
            $generatedCode .= "    ),\n";
        }
        $generatedCode .= "];\n";

        $methods = [
            new MethodGenerator(
                name: 'getEntityTypeDefinitions',
                body: $generatedCode,
            )->setReturnType('array'),
        ];

        $this->dump('/Entity/EntityTypeDefinitionRepository.php', new LaminasClassGenerator(
            name: 'EntityTypeDefinitionRepository',
            namespaceName: static::GENERATED_CLASS_NAMESPACE.'Entity',
            extends: AbstractEntityTypeDefinitionRepository::class,
            methods: $methods,
        ));
    }

    protected function getShortClassName(string $fullClassName): string
    {
        return substr($fullClassName, strrpos($fullClassName, '\\') + 1);
    }

    protected function dump($classPath, LaminasClassGenerator $classGenerator): void
    {
        file_put_contents($this->generatedBaseDirectory.$classPath, new FileGenerator([
            'classes' => [$classGenerator],
        ])->generate());
    }

    protected function safeVarExport(mixed $variable): string
    {
        if (is_int($variable) || is_string($variable) || is_bool($variable)) {
            return var_export($variable, true);
        } elseif ($variable instanceof Number) {
            return "new \\BcMath\\Number('{$variable}')";
        }

        throw new \Exception('Unsupported parameter type');
    }
}
