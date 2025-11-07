<?php

namespace Sarue\Orm\Query;

interface QueryInterface
{
    const string ENTITY_CLASS = '';

    public function loadAll();
    public function loadOne();
    public function count();
    public function sum();
}
