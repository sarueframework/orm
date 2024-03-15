<?php

namespace Sarue\Server\SarueServerBundle\Entity;

use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use Sarue\Orm\Exception\InvalidDefinitionException;
use Sarue\Orm\Field\FieldFactory;
use Sarue\Orm\Field\FieldInterface;
use Sarue\Orm\Validator\StringValidator\SnakeCaseValidator;

class EntityType implements EntityTypeInterface
{
    public static function createFromDefinition(string $entityTypeName, array $definition): static
    {
        if (!SnakeCaseValidator::validate($entityTypeName)) {
            throw new InvalidConfigurationException("The entity type name $entityTypeName should be in snake_case and start with a letter");
        }

        // @todo Properly use dependency injection for FieldFactory.
        $fieldFactory = new FieldFactory();

        if (empty($definition['fields']) || !is_array($definition['fields'])) {
            throw new InvalidDefinitionException("Entity $entityTypeName has no no fields in its definition");
        }

        ksort($definition['fields']);

        $fields = [];
        foreach ($definition['fields'] as $fieldName => $fieldDefinition) {
            $fields[$fieldName] = $fieldFactory->createFromDefinition($fieldName, $fieldDefinition);
        }

        return new static($entityTypeName, $fields);
    }

    public static function createFromSchemaStorage(string $entityTypeName, array $fieldsFromStorage): static
    {
        $fields = [];
        foreach ($fieldsFromStorage as $fieldName => $fieldFromStorage) {
            $fields[$fieldName] = $fieldFromStorage['class']::createFromSchemaStorage(
                $fieldName,
                $fieldFromStorage['schema'],
                $fieldFromStorage['additional'],
                $fieldFromStorage['required'],
            );
        }

        return new static($entityTypeName, $fields);
    }

    public function __construct(
        protected string $entityTypeName,
        protected array $fields,
    ) {}

    public function getName(): string
    {
        return $this->entityTypeName;
    }

    public function getField(string $fieldName): FieldInterface
    {
        if (!isset($this->fields[$fieldName])) {
            throw new \Exception("Field $fieldName does not exist in entity " . $this->getName());
        }

        return $this->fields[$fieldName];
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
