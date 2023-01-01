<?php

declare(strict_types=1);

if (!defined('PRECISAO_SOLIDBASE')) {
    $scale = 14;
    define('PRECISAO_SOLIDBASE', $scale);
}

if (!function_exists('modulo')) {
    function modulo(int|float $valor): int|float
    {
        return abs($valor);
    }
}
if (!function_exists('mod')) {
    function mod(int|float $valor1, int|float $valor2): float
    {
        return fmod($valor1, $valor2);
    }
}

if (!function_exists('eIgual')) {
    function eIgual(int|float $valor1, int|float $valor2, $estrito = false): bool
    {
        if ($estrito) {
            return $valor1 === $valor2;
        }

        return eZero($valor1 - $valor2);
    }
}

if (!function_exists('eZero')) {
    function eZero(int|float $valor): bool
    {
        return entre(-ZERO_SOLIDBASE, $valor, ZERO_SOLIDBASE);
    }
}
if (!function_exists('arredondar')) {
    function arredondar(int|float $numero, int $casas): int|float
    {
        return round($numero, $casas);
    }
}
if (!function_exists('entre')) {
    function entre(
        int|float $valorEsquerda,
        int|float $valorComparacao,
        int|float $valorDireita,
        bool $limitesIncluso = true
    ): bool {
        if (!$limitesIncluso) {
            if (eIgual($valorEsquerda, $valorComparacao)) {
                return false;
            }
            if (eIgual($valorComparacao, $valorDireita)) {
                return false;
            }
        }
        if (eMaior($valorEsquerda, $valorComparacao)) {
            return false;
        }
        if (eMenor($valorDireita, $valorComparacao)) {
            return false;
        }

        return true;
    }
}
if (!function_exists('eInteiro')) {
    function eInteiro(int|float $valor): bool
    {
        $numero = round(abs($valor), 0);
        $numeroEsquerda = abs($valor) - ZERO_SOLIDBASE;
        $numerDireita = abs($valor) + ZERO_SOLIDBASE;

        return entre($numeroEsquerda, $numero, $numerDireita);
    }
}
if (!function_exists('normalizar')) {
    function normalizar(int|float $valor): float|int
    {
        return eInteiro($valor) ? round($valor, 0) : $valor;
    }
}
if (!function_exists('eMenor')) {
    function eMenor(int|float $valor1, int|float $valor2): bool
    {
        return $valor1 < $valor2;
    }
}

if (!function_exists('eMaior')) {
    function eMaior(int|float $valor1, int|float $valor2): bool
    {
        return $valor1 > $valor2;
    }
}

if (!function_exists('ePositivo')) {
    function ePositivo(int|float $valor1): bool
    {
        return eMaior($valor1, 0);
    }
}

if (!function_exists('eZeroOuPositivo')) {
    function eZeroOuPositivo(int|float $valor1): bool
    {
        return eZero($valor1) || eMaior($valor1, 0);
    }
}

if (!function_exists('eNegativo')) {
    function eNegativo(int|float $valor1): bool
    {
        return eMenor($valor1, 0);
    }
}

if (!function_exists('eZeroOuNegativo')) {
    function eZeroOuNegativo(int|float $valor1): bool
    {
        return eZero($valor1) || eMenor($valor1, 0);
    }
}

if (!function_exists('comparar')) {
    function comparar(int|float $valor1, int|float $valor2): int
    {
        return $valor1 <=> $valor2;
    }
}

if (!function_exists("zeroSolidbase")) {
    function zeroSolidbase(int $escala): float
    {
        $precisao = (int) max($escala / 2, 9);
        $zero = 1 / (10 ** $precisao);
        return $zero;
    }
}
if (!defined('ZERO_SOLIDBASE')) {
    $scale = defined("PRECISAO_SOLIDBASE") ? PRECISAO_SOLIDBASE : 14;
    $zero = zeroSolidbase($scale);
    define('ZERO_SOLIDBASE', $zero);
}
