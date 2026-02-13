<?php

namespace Sarue\Orm\Generator\Laminas;

use Laminas\Code\Generator\PropertyGenerator;

class PropertyGeneratorWithTypedConstant extends PropertyGenerator
{
    public function generate()
    {
        if (!$this->isConst()) {
            return parent::generate();
        }

        $name = $this->getName();
        $defaultValue = $this->getDefaultValue();

        $output = '';

        if (($docBlock = $this->getDocBlock()) !== null) {
            $docBlock->setIndentation('    ');
            $output .= $docBlock->generate();
        }

        if (null !== $defaultValue && !$defaultValue->isValidConstantType()) {
            throw new \RuntimeException(sprintf('The property %s is said to be constant but does not have a valid constant value.', $this->name));
        }

        return $output.
            $this->indentation.
            ($this->isFinal() ? 'final ' : '').
            $this->getVisibility().
            ' const '.
            ($this->type ? $this->type->generate().' ' : '').
            $name.' = '.
            (null !== $defaultValue ? $defaultValue->generate() : 'null;');
    }
}
