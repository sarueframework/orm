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

    public function testGetFields(): void
    {
        $fields = $this->entityType()->getFields();
        $this->assertEquals(['field'], array_keys($fields));
        $this->assertInstanceOf(FieldInterface::class, $fields['field']);
    }

    public function testToStorage(): void
    {
        $entityType = $this->entityType();

        /** @var \PHPUnit\Framework\MockObject */
        $field = $entityType->getField('field');
        $field
            ->expects($this->once())
            ->method('getSchema')
            ->willReturn(['schema' => 123]);
        $field
            ->expects($this->once())
            ->method('getProperties')
            ->willReturn(['property' => 456]);
        $field
            ->expects($this->once())
            ->method('getAdditionalDefinition')
            ->willReturn(['additional' => 789]);
        $field
            ->expects($this->once())
            ->method('isRequired')
            ->willReturn(true);

        $this->assertEquals([
            'fields' => [
                'field' => [
                    'class' => get_class($field),
                    'schema' => ['schema' => 123],
                    'properties' => ['property' => 456],
                    'additional' => ['additional' => 789],
                    'required' => true,
                ],
            ],
        ], $entityType->toStorage());
    }

    protected function entityType(): EntityType
    {
        return new EntityType(
            'person',
            ['field' => $this->createMock(FieldInterface::class)],
        );
    }
}
