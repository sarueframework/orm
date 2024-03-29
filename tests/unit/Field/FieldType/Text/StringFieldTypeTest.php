<?php

namespace Sarue\Orm\Tests\Unit\Field\FieldType\Text;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Sarue\Orm\Field\FieldBase;
use Sarue\Orm\Field\FieldFactory;
use Sarue\Orm\Field\FieldType\Text\StringFieldType;
use Sarue\Orm\Validator\StringValidator\SnakeCaseValidator;

#[CoversClass(StringFieldType::class)]
#[UsesClass(FieldBase::class)]
#[UsesClass(FieldFactory::class)]
#[UsesClass(SnakeCaseValidator::class)]
class StringFieldTypeTest extends TestCase
{
    public function testInstantiation(): void
    {
        $fieldFactory = new FieldFactory();
        $field = $fieldFactory->createFromDefinition('field_name', ['type' => 'string']);
        $this->assertInstanceOf(StringFieldType::class, $field);
    }
}
