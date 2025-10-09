<?php

namespace Sarue\Orm\Schema;

use Sarue\Orm\Field\FieldInterface;

class ManagerDumper {
    public function __construct(
        protected string $baseDirectory,
        protected string $entityManagerNamespace = 'App\\Entity\\Manager',
    ) {}

    public function dumpManagerForEntity(string $fullEntityName): void {
        $reflection = new \ReflectionClass($fullEntityName);

        $entityNameParts = explode('\\', $fullEntityName);
        $managerClassName = array_pop($entityNameParts) . 'Manager';

        $output = "<?php\n\nnamespace {$this->entityManagerNamespace};\n\nclass $managerClassName extends \\Sarue\\Orm\\Schema\\ManagerBase {\n";
        $fieldsFunction = "    /**\n     * @return string[]\n     */\n    public function getFieldList(): array {\n        return [\n";

        foreach ($reflection->getProperties() as $property) {
            $type = $property->getType()->__toString();
            if (class_exists($type) && (is_subclass_of($type, FieldInterface::class))) {
                $output .= '    public readonly \\' . $type::getManagerClass() . ' $' . $property->getName() . ";\n";
                $fieldsFunction .= "            '{$property->getName()}',\n";
            }
        }

        $output .= "\n{$fieldsFunction}        ];\n    }\n}";
        var_dump($this->baseDirectory . '/' . $managerClassName . '.php');
        file_put_contents($this->baseDirectory . '/' . $managerClassName . '.php', $output);
    }
}
