<?php

namespace Sarue\Orm\Tests\Unit\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Exception\NonExistingFieldException;
use Sarue\Orm\Field\FieldInterface;

#[CoversClass(EntityType::class)]
class EntityTypeTest extends TestCase
{
    protected EntityType $entityType;
    protected FieldInterface&MockObject $field;

    public function setUp(): void
    {
        $this->field = $this->createMock(FieldInterface::class);
        $this->entityType = new EntityType('person', ['field' => $this->field]);

    }

    public function testGetName(): void
    {
        $this->assertEquals('person', $this->entityType->getName());
    }

    public function testGetField(): void
    {
        $this->assertInstanceOf(FieldInterface::class, $this->entityType->getField('field'));
    }

    public function testGetFieldException(): void
    {
        $this->expectException(NonExistingFieldException::class);
        $this->expectExceptionMessage('Field invalid_field does not exist in entity person');

        $this->entityType->getField('invalid_field');
    }

    public function testGetFields(): void
    {
        $fields = $this->entityType->getFields();
        $this->assertEquals(['field'], array_keys($fields));
        $this->assertInstanceOf(FieldInterface::class, $fields['field']);
    }

    public function testToStorage(): void
    {
        /** @var \PHPUnit\Framework\MockObject */
        $field = $this->entityType->getField('field');
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
        ], $this->entityType->toStorage());
    }
}
