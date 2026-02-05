<?php

namespace Sarue\Orm\Tests\Integration\Dummy\ExceptionEntity\MultipleAttributesInProperty;

use Sarue\Orm\Entity\AbstractLogEntity;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Field\Type\Numeric\IntegerField;
use Sarue\Orm\Field\Type\Text\TextField;

#[EntityType]
class MultipleAttributesInPropertyEntity extends AbstractLogEntity
{
    #[TextField]
    #[IntegerField()]
    public string $message;
}
