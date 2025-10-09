<?php

namespace App\Entity\Manager;

class DummyEntityManager extends \Sarue\Orm\Schema\ManagerBase {
    public readonly \Sarue\Orm\Field\Type\Numeric\IntegerManager $age;
    public readonly \Sarue\Orm\Field\Type\Text\TextManager $name;

    /**
     * @return string[]
     */
    public function getFieldList(): array {
        return [
            'age',
            'name',
        ];
    }
}