<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sarue\Orm\Validator\StringValidator\SnakeCaseValidator;

#[CoversClass(SnakeCaseValidator::class)]
class SnakeCaseValidatorTest extends TestCase
{
    public static function dataProviderTestValidate(): array
    {
        return [
            ['snake_case', true],
            ['__still_snake_case', true],
            ['0000_yet_another_snake_case_9999', true],
            ['invalid_snake_case%', false],
            ['invalid_snake_case!!!', false],
            ['camelCase', false],
            ['CamelCase', false],
            ['Not_Snake_Case', false],
        ];
    }

    #[DataProvider('dataProviderTestValidate')]
    public function testValidate(string $string, bool $valid): void
    {
        $response = SnakeCaseValidator::validate($string);
        if ($valid) {
            $this->assertTrue($response);
        } else {
            $this->assertFalse($response);
        }
    }

    public static function dataProviderTestValidateStartingWithLetter(): array
    {
        return [
            ['snake_case', true],
            ['__still_snake_case', false],
            ['0000_yet_another_snake_case_9999', false],
            ['invalid_snake_case%', false],
            ['invalid_snake_case!!!', false],
            ['camelCase', false],
            ['CamelCase', false],
            ['Not_Snake_Case', false],
        ];
    }

    #[DataProvider('dataProviderTestValidateStartingWithLetter')]
    public function testValidateStartingWithLetter(string $string, bool $valid): void
    {
        $response = SnakeCaseValidator::validateStartingWithLetter($string);
        if ($valid) {
            $this->assertTrue($response);
        } else {
            $this->assertFalse($response);
        }
    }
}
