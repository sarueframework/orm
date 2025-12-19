<?php

namespace Sarue\Orm\Tests\Integration\Dummy\Entity;

use BcMath\Number;
use Sarue\Orm\Entity\EntityBase;
use Sarue\Orm\Field\Type\Numeric\DecimalField;
use Sarue\Orm\Field\Type\Numeric\IntegerField;
use Sarue\Orm\Field\Type\Text\TextField;
use Sarue\Orm\Schema\EntityDefinition;

#[EntityDefinition]
class DummyEntity extends EntityBase
{
    #[IntegerField(minimum: 0, maximum: 123)]
    public ?int $age;

    #[DecimalField(minimum: new Number('0.1'))]
    public Number $height;

    #[TextField]
    public string $name = '';
}
