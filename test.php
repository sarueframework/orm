<?php

class AA
{
    public string $name {
        get => '('.$this->name.')';
        set(string $value) {
            $this->name = $value;
        }
    }
}

class BB extends AA
{
    public string $name {
        get => '['.$this->name.']';
        set(string $value) {
            parent::$name::set("\\\\$value//");
        }
    }

    public string $nome;

    public function __set($property, $value)
    {
        print_r([$property, $value]);
    }
}

$b = new BB();
$b->name = 'john';
$b->nome = 'john';

echo var_export($b);

/*
$person->set([
    'firstName' => 'John',
    'lastName' => 'Smith',
    'geolocation' => [
        -23.461659327809617,
        -46.438786406581585
    ],
]);

$person->patch([
    'firstName' => 'John',
    'lastName' => 'Smith',
    'geolocation' => [
        -23.461659327809617,
        -46.438786406581585
    ],
]);
*/

// A, deprecated as of PHP 8.2.0

// Type 6: Objects implementing __invoke can be used as callables
class C
{
    public function __invoke(array $name)
    {
        echo 'Hello ', $name, PHP_EOL;
    }

    public function get(array $aa)
    {
    }
}

$c = new C();

$c(324134123);
