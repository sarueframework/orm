<?php

namespace Sarue\Orm\Tests\Integration\Dummy\SimpleEntity;

use Sarue\Orm\Entity\AbstractLogEntity;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Field\Type\Text\TextField;

#[EntityType]
class DummyLogEntity extends AbstractLogEntity
{
    #[TextField]
    public string $message;

    public string $notAField;
}
