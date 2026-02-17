<?php

namespace Sarue\Orm\Query\Condition\Text;

use Sarue\Orm\Query\Condition\AbstractFieldCondition;
use Sarue\Orm\Query\Parameter\TextParameter;

class PatternMatchingCondition extends AbstractFieldCondition implements TextConditionInterface
{
    public static function startsWith(string $text, bool $caseInsensitive = false): TextConditionInterface
    {
        return new self(static::escapeWildcards($text).'%', self::getLikeOperator($caseInsensitive, false));
    }

    public static function doesNotStartWith(string $text, bool $caseInsensitive = false): TextConditionInterface
    {
        return new self(static::escapeWildcards($text).'%', self::getLikeOperator($caseInsensitive, true));
    }

    public static function endsWith(string $text, bool $caseInsensitive = false): TextConditionInterface
    {
        return new self('%'.static::escapeWildcards($text), self::getLikeOperator($caseInsensitive, false));
    }

    public static function doesNotEndWith(string $text, bool $caseInsensitive = false): TextConditionInterface
    {
        return new self('%'.static::escapeWildcards($text), self::getLikeOperator($caseInsensitive, true));
    }

    public static function contains(string $text, bool $caseInsensitive = false): TextConditionInterface
    {
        return new self('%'.static::escapeWildcards($text).'%', self::getLikeOperator($caseInsensitive, false));
    }

    public static function doesNotContain(string $text, bool $caseInsensitive = false): TextConditionInterface
    {
        return new self('%'.static::escapeWildcards($text).'%', self::getLikeOperator($caseInsensitive, true));
    }

    public static function like(string|UnescapedText $text, bool $caseInsensitive = false): TextConditionInterface
    {
        return new self(self::processPotentiallyUnescapedText($text), self::getLikeOperator($caseInsensitive, false));
    }

    public static function notLike(string|UnescapedText $text, bool $caseInsensitive = false): TextConditionInterface
    {
        return new self(self::processPotentiallyUnescapedText($text), self::getLikeOperator($caseInsensitive, true));
    }

    public static function escapeWildcards(string $text): string
    {
        return strtr($text, [
            '\\' => '\\\\',
            '_' => '\\_',
            '%' => '\\%',
        ]);
    }

    private static function getLikeOperator(bool $caseInsensitive, bool $negate): PatternMatchingOperator
    {
        return $caseInsensitive ?
            ($negate ? PatternMatchingOperator::NotILike : PatternMatchingOperator::ILike) :
            ($negate ? PatternMatchingOperator::NotLike : PatternMatchingOperator::Like);
    }

    private static function processPotentiallyUnescapedText(string|UnescapedText $text): string
    {
        if ($text instanceof UnescapedText) {
            return (string) $text;
        }

        return self::escapeWildcards($text);
    }

    private function __construct(
        public readonly string $comparisonText,
        public PatternMatchingOperator $operator,
    ) {
    }

    public function buildSql(): array
    {
        return [
            $this->fieldName,
            ' ',
            $this->operator->value,
            ' ',
            new TextParameter($this->comparisonText),
        ];
    }
}
