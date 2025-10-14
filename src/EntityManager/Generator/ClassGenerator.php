<?php

namespace Sarue\Orm\EntityManager\Generator;

use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Field\FieldInterface;

class ClassGenerator {
    public function __construct(
        protected string $entityDirectory,
        protected string $generatedBaseDirectory,
        protected string $entityNamespace = 'App\\Entity\\',
        protected string $generatedNamespace = 'App\\Generated\\Sarue\\',
    ) {}

    public function generateClasses(): void {
        $phpFiles = array_filter(
            scandir($this->entityDirectory),
            fn($filename) => str_ends_with($filename, '.php'),
        );

        $possibleClasses = array_map(
            fn($filename) => $this->entityNamespace . substr($filename, 0, -4),
            $phpFiles,
        );

        $entityClasses = array_filter(
            $possibleClasses,
            fn($className) => is_subclass_of($className, EntityInterface::class) && !(new \ReflectionClass($className))->isAbstract(),
        );

        $queryClasses = [];
        foreach ($entityClasses as $entityClass) {
            $queryClasses[] = $this->dumpQueryForEntity($entityClass);
        }

        $this->dumpQueryFactory($queryClasses);
    }

    public function dumpQueryForEntity(string $entityFullClassName): string {
        $reflection = new \ReflectionClass($entityFullClassName);

        $entityNameParts = explode('\\', $entityFullClassName);
        $queryClassName = array_pop($entityNameParts) . 'Query';

        $namespace = $this->generatedNamespace . 'Entity\\Query';

        $output = "<?php\n\nnamespace $namespace;\n\nclass $queryClassName extends \\Sarue\\Orm\\Query\\QueryBase {\n";
        $getFieldListFunction = "public function getFieldList(): array {\nreturn [\n";

        foreach ($reflection->getProperties() as $property) {
            $type = $property->getType()->__toString();
            if (class_exists($type) && (is_subclass_of($type, FieldInterface::class))) {
                $conditionType = [$type, 'getConditionType']();
                $output .= "public function {$property->getName()}(\\{$conditionType} \$condition): static { return \$this->addCondition(\$condition); }\n";
                $getFieldListFunction .= "'{$property->getName()}',\n";
            }
        }

        $output .= "\n{$getFieldListFunction}        ];\n    }\n}\n";
        file_put_contents($this->generatedBaseDirectory . '/Entity/Query/' . $queryClassName . '.php', $output);

        return $namespace .'\\' . $queryClassName;
    }

    public function dumpQueryFactory(array $queryClasses): void {
        $namespace = $this->generatedNamespace . 'Entity\\Query';
        $output = "<?php\n\nnamespace $namespace;\n\nclass QueryFactory extends \\Sarue\\Orm\\Query\\QueryFactoryBase {\n";
        $getEntityListFunction = "public function getEntityList(): array {\nreturn [\n";

        foreach ($queryClasses as $queryClass) {
            $className = substr($queryClass, strrpos($queryClass, '\\') + 1);
            $getEntityListFunction .= "'" . str_replace('\\', '\\\\', $queryClass) . "',\n";
            $output .= "function get$className(): \\$queryClass { return \$this->instantiateQuery('$queryClass'); }\n";
        }
        $getEntityListFunction .= "];\n}\n";
        $output .= $getEntityListFunction . "}\n";
        file_put_contents($this->generatedBaseDirectory . '/Entity/Query/QueryFactory.php', $output);
    }
}
