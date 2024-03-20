<?php

namespace Sarue\Orm\Event;

final class FieldTypeClassResolutionEvent
{
    public function __construct(
        protected string $type,
        protected ?string $class,
    ) {}

    public function getType(): string
    {
        return $this->class;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): FieldTypeClassResolutionEvent
    {
        $this->class = $class;

        return $this;
    }
}
