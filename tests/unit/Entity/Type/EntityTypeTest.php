<?php

namespace Sarue\Orm\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Exception\NonExistingFieldException;
use Sarue\Orm\Field\FieldInterface;

class EntityTypeTest extends TestCase
{
    /**
     * Tests getName().
     */
    public function testGetName(): void
    {
        $this->assertEquals('person', $this->entityType()->getName());
    }

    /**
     * Tests getField().
     */
    public function testGetField(): void
    {
        $this->assertInstanceOf(FieldInterface::class, $this->entityType()->getField('field'));
    }

    /**
     * Tests getField() with invalid field.
     */
    public function testGetFieldException(): void
    {
        $this->expectException(NonExistingFieldException::class);
        $this->expectExceptionMessage('Field invalid_field does not exist in entity person');

        $this->entityType()->getField('invalid_field');
    }

    /**
     * Tests getField().
     */
    public function testGetFields(): void
    {
        $fields = $this->entityType()->getFields();
        $this->assertEquals(['field'], array_keys($fields));
        $this->assertInstanceOf(FieldInterface::class, $fields['field']);
    }

    protected function entityType(): EntityType
    {
        return new EntityType(
            'person',
            ['field' => $this->createMock(FieldInterface::class)],
        );
    }
}
