<?php

namespace Sarue\Orm\Tests\Unit\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sarue\Orm\Event\FieldTypeClassResolutionEvent;
use Sarue\Orm\Field\FieldType\Text\StringFieldType;

#[CoversClass(FieldTypeClassResolutionEvent::class)]
class FieldTypeClassResolutionEventTest extends TestCase
{
    public function testEvent(): void
    {
        $event = new FieldTypeClassResolutionEvent('string', StringFieldType::class);
        $this->assertEquals('string', $event->getType());
        $this->assertEquals(StringFieldType::class, $event->getClass());

        $event->setClass(self::class);
        $this->assertEquals(self::class, $event->getClass());
    }
}
