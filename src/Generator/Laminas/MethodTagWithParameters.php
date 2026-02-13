<?php

namespace Sarue\Orm\Generator\Laminas;

use Laminas\Code\Generator\DocBlock\Tag\MethodTag;
use Laminas\Code\Generator\ParameterGenerator;

class MethodTagWithParameters extends MethodTag
{
    protected array $parameters;

    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function generateParameters(): string
    {
        return implode(', ', array_map(
            static fn (ParameterGenerator $parameter): string => $parameter->generate(),
            $this->parameters
        ));
    }

    public function generate()
    {
        return '@method'.
            ($this->isStatic ? ' static' : '').
            (!empty($this->types) ? ' '.$this->getTypesAsString() : '').
            $this->methodName.'('.
            (!empty($this->parameters) ? $this->generateParameters() : '').
            ')'.
            (!empty($this->description) ? ' '.$this->description : '');
    }
}
