```php
$query = $entityManager
    ->getQuery()
    ->where('name')->startsWith('B');
    ->where('salary')->startsWith('C');


$query = (new EmployeeQuery())
    ->name->startsWith('A'))
    ->salary->isLargerThan(20))
    ->location->lattitude->isLargerThan(123))
    ->department->name->
    ->orGroup()
        ->name->startsWith('B'))
        ->name->startsWith('C'))
        ->end()
    ->department->leftJoin('first_department')->
    ->range(123);
```



```php
$entityManager->getEmployeeQuery()
    ->where()
        ->addCondition(new StartsWith('A', 'name'));



$query = $entityManager->getEmployeeQuery();
$query->whereName()->is('Rebecca');
$query->whereColor()->is('blue');


$entityManager->getEmployeeQuery()
    ->whereName(isExactly('Rebecca'))
    ->whereLastName(startsWith('B'));

$employees = $entityManager->queryEmployee()
    ->whereName(startsWith('A'))
    ->whereSalary(greaterThan(2000))
    ->load();

$employees = $entityManager->queryEmployee()
    ->orConditionGroup()
        ->whereName(startsWith('A'))
        ->whereName(startsWith('B'))
        ->end()
    ->whereSalary(greaterThan(2000))
    ->load();

$employees = $entityManager->queryEmployee()
    ->whereName(new StartsWith('A'))
    ->whereSalary(new GreaterThan(2000))
    ->load();

$employees = $entityManager->queryEmployee()
    ->whereName()->startsWith('A')
    ->whereSalary()->greaterThan(2000)
    ->load();
```


```
/api/employee?filter[name]=John&department=123
```

```php
abstract function applyFilterName(string $value): void;
abstract function applyFilterDepartment(string $value): void;
```
