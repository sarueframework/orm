<?php

namespace Sarue\Orm\Tests\Integration\Dummy\Entity;

use Sarue\Orm\Attribute\Entity;
use Sarue\Orm\Attribute\Field;
use Sarue\Orm\Entity\EntityBase;

#[Entity(some: "thing")]
class DummyEntity extends EntityBase
{
    #[Field]
    public int $age;

    #[Field]
    public string $name;
}
