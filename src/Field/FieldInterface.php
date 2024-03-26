<?php

namespace Sarue\Orm\Field;

interface FieldInterface
{
    /**
     * Parses a definition, separating schema from additional definitions.
     *
     * @param string  $type          the field type
     * @param mixed[] $rawDefinition the definition of the field provided by the developer
     *
     * @return array{0: mixed[], 1: mixed[], 2: mixed[], 3: bool} an array with [$schema, $properties, $additionalOptions, $required]
     */
    public static function parseDefinition(string $type, array $rawDefinition): array;

    /**
     * @param mixed[] $schema               the schema-related properties, properly cleanedup and processed
     * @param mixed[] $properties           the non-schema-related properties, properly cleanedup and processed
     * @param mixed[] $additionalDefinition anything in the "additional:" part of the definition
     * @param bool    $required             whether the field is required or not
     */
    public function __construct(
        string $fieldName,
        array $schema,
        array $properties,
        array $additionalDefinition,
        bool $required,
    );

    /**
     * Gets the field name.
     *
     * @return string the field name, that will be in snake_case
     */
    public function getFieldName(): string;

    /**
     * @return mixed[] the options in the definition that affect the database structure
     */
    public function getSchema(): array;

    /**
     * @return mixed[] the options in the definition that DO NOT affect the database structure
     */
    public function getProperties(): array;

    /**
     * @return mixed[] the "additional:" part of definition, used to impart any configuration not directly used by the
     *                 FieldInterface-implementing class
     */
    public function getAdditionalDefinition(): array;

    /**
     * @return bool whether the field is required or not
     */
    public function isRequired(): bool;

    // @todo: add methods schema(), validate(), prepareToStorage().
}
