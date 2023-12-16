<?php

declare(strict_types=1);

if (!defined('ACCURACY_SOLIDBASE')) {
    $scale = 14;
    define('ACCURACY_SOLIDBASE', $scale);
}

if (!function_exists('sbModule')) {
    function sbModule(int|float $number): int|float
    {
        return abs($number);
    }
}
if (!function_exists('sbMod')) {
    function sbMod(int|float $number1, int|float $number2): float
    {
        return fmod($number1, $number2);
    }
}

if (!function_exists('sbEquals')) {
    function sbEquals(int|float $number1, int|float $number2, $strict = false): bool
    {
        if ($strict) {
            return $number1 === $number2;
        }

        return sbIsZero($number1 - $number2);
    }
}

if (!function_exists('sbIsZero')) {
    function sbIsZero(int|float $number): bool
    {
        return sbBetween(-ZERO_SOLIDBASE, $number, ZERO_SOLIDBASE);
    }
}
if (!function_exists('sbRound')) {
    function sbRound(int|float $number, int $precision): int|float
    {
        return round($number, $precision);
    }
}
if (!function_exists('sbBetween')) {
    function sbBetween(
        int|float $valorEsquerda,
        int|float $valorComparacao,
        int|float $valorDireita,
        bool $limitesIncluso = true
    ): bool {
        if (!$limitesIncluso) {
            if (sbEquals($valorEsquerda, $valorComparacao)) {
                return false;
            }
            if (sbEquals($valorComparacao, $valorDireita)) {
                return false;
            }
        }
        if (sbBiggerThen($valorEsquerda, $valorComparacao)) {
            return false;
        }
        if (sbLessThan($valorDireita, $valorComparacao)) {
            return false;
        }

        return true;
    }
}
if (!function_exists('sbIsInteger')) {
    function sbIsInteger(int|float $value): bool
    {
        $number = round(abs($value), 0);
        $numberLeft = abs($value) - ZERO_SOLIDBASE;
        $numberRight = abs($value) + ZERO_SOLIDBASE;

        return sbBetween($numberLeft, $number, $numberRight);
    }
}
if (!function_exists('sbNormalize')) {
    function sbNormalize(int|float $value): float|int
    {
        return sbIsInteger($value) ? round($value, 0) : $value;
    }
}
if (!function_exists('sbLessThan')) {
    function sbLessThan(int|float $value1, int|float $value2): bool
    {
        return $value1 < $value2;
    }
}

if (!function_exists('sbBiggerThen')) {
    function sbBiggerThen(int|float $value1, int|float $value2): bool
    {
        return $value1 > $value2;
    }
}

if (!function_exists('sbIsPositive')) {
    function sbIsPositive(int|float $value): bool
    {
        return sbBiggerThen($value, 0);
    }
}

if (!function_exists('sbIsZeroOrPositive')) {
    function sbIsZeroOrPositive(int|float $value): bool
    {
        return sbIsZero($value) || sbBiggerThen($value, 0);
    }
}

if (!function_exists('sbIsNegative')) {
    function sbIsNegative(int|float $value): bool
    {
        return sbLessThan($value, 0);
    }
}

if (!function_exists('sbIsZeroOrNegative')) {
    function sbIsZeroOrNegative(int|float $value): bool
    {
        return sbIsZero($value) || sbLessThan($value, 0);
    }
}

if (!function_exists('sbCompare')) {
    function sbCompare(int|float $value1, int|float $value2): int
    {
        return $value1 <=> $value2;
    }
}

if (!function_exists("sbZeroSolidbase")) {
    function sbZeroSolidbase(int $scala): float
    {
        $acuraccy = (int) max($scala / 2, 9);
        $zero = 1 / (10 ** $acuraccy);
        return $zero;
    }
}
if (!defined('ZERO_SOLIDBASE')) {
    $scale = defined("ACCURACY_SOLIDBASE") ? ACCURACY_SOLIDBASE : 14;
    $zero = sbZeroSolidbase($scale);
    define('ZERO_SOLIDBASE', $zero);
}
