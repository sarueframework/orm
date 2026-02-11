<?php

namespace Sarue\Orm\Tests\Integration\Dummy\Entity;

use Sarue\Orm\Entity\AbstractEntity;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Field\Type\Numeric\IntegerField;
use Sarue\Orm\Field\Type\Text\TextField;

#[EntityType]
class DummyEntity extends AbstractEntity
{
    #[TextField]
    public string $name;

    #[IntegerField]
    public ?int $yearOfBirth;
}
