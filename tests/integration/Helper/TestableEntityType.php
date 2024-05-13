<?php

namespace Sarue\Orm\Tests\Integration\Helper;

use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Field\FieldBase;

class TestableEntityType extends EntityType
{
    public function __construct()
    {
        parent::__construct(
            'company',
            [
                'name' => new TestableField(
                    'name',
                    [],
                    [],
                    true,
                    'name TEXT',
                ),
                'cnpj' => new TestableField(
                    'cnpj',
                    [],
                    [],
                    true,
                    'cnpj char(14)',
                ),
                'main_area' => new TestableField(
                    'name',
                    [],
                    [],
                    true,
                    'main_area TEXT',
                ),
            ],
        );
    }
}
