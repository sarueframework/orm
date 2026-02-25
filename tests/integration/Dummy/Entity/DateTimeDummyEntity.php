<?php

namespace Sarue\Orm\Tests\Integration\Dummy\Entity;

use Sarue\Orm\Entity\AbstractLogEntity;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Field\Type\DateTime\DateField;

#[EntityType]
class DateTimeDummyEntity extends AbstractLogEntity
{
    #[DateField]
    public \DateTime $date;
}
