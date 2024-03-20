<?php

namespace Sarue\Orm\Validator\StringValidator;

final class SnakeCaseValidator
{
    /**
     * Validates that the string is snake_case.
     */
    public static function validate(string $string): bool
    {
        return preg_match('/^[a-z0-9\_]+$/', $string);
    }

    /**
     * Validates that the string is snake_case and starts with a letter.
     */
    public static function validateStartingWithLetter(string $string): bool
    {
        return self::validate($string) && preg_match('/^[a-z]$/', substr($string, 0, 1));
    }
}
