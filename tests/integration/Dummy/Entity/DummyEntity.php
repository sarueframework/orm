<?php

namespace Sarue\Orm\Tests\Integration\Dummy\Entity;

use Sarue\Orm\Attribute\Entity;
use Sarue\Orm\Entity\EntityBase;
use Sarue\Orm\Field\Type\Numeric\IntegerField;
use Sarue\Orm\Field\Type\Text\TextField;

#[Entity(some: 'thing')]
class DummyEntity extends EntityBase
{
    #[IntegerField(minimum: 0, maximum: 123)]
    public int $age;

    #[TextField]
    public string $name;
}
