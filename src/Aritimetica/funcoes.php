<?php

declare(strict_types=1);

use SolidBase\Matematica\Aritimetica\Numero;

if (!function_exists('somar')) {
    function somar(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->somar($valor2);
    }
}
if (!function_exists('numero')) {
    function numero(int|float|Numero $valor): Numero
    {
        return is_a($valor, Numero::class) ? clone $valor : new Numero($valor);
    }
}

if (!function_exists('subtrair')) {
    function subtrair(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->subtrair($valor2);
    }
}

if (!function_exists('multiplicar')) {
    function multiplicar(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->multiplicar($valor2);
    }
}

if (!function_exists('dividir')) {
    function dividir(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->dividir($valor2);
    }
}
if (!function_exists('modulo')) {
    function modulo(int|float|Numero $valor): Numero
    {
        $numero = numero($valor);

        return $numero->modulo();
    }
}
if (!function_exists('mod')) {
    function mod(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->mod($valor2);
    }
}

if (!function_exists('eIgual')) {
    function eIgual(int|float|Numero $valor1, int|float|Numero $valor2): bool
    {
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

        return $numero->eIgual($valor2);
    }
}

if (!function_exists('eZero')) {
    function eZero(int|float|Numero $valor): bool
    {
        return eIgual($valor, 0);
    }
}

if (!function_exists('potencia')) {
    function potencia(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->potencia($valor2);
    }
}

if (!function_exists('eMenor')) {
    function eMenor(int|float|Numero $valor1, int|float|Numero $valor2): bool
    {
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

        return $numero->eMenor($valor2);
    }
}

if (!function_exists('eMaior')) {
    function eMaior(int|float|Numero $valor1, int|float|Numero $valor2): bool
    {
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

        return $numero->eMaior($valor2);
    }
}

if (!function_exists('comparar')) {
    function comparar(int|float|Numero $valor1, int|float|Numero $valor2): int
    {
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

        return $numero->comparar($valor2);
    }
}

if (!function_exists('fatorial')) {
    function fatorial(int|Numero $numero): Numero
    {
        $fatorial = numero($numero);

        while ($numero > 1) {
            $fatorial->multiplicar(--$numero);
        }

        return $fatorial;
    }
}

if (!function_exists('seno')) {
    function seno(float $angulo): Numero
    {
        return numero(sin($angulo));
    }
}

if (!function_exists('cosseno')) {
    function cosseno(float $angulo): Numero
    {
        return numero(cos($angulo));
    }
}

if (!function_exists('tangente')) {
    function tangente(float|Numero $angulo): Numero
    {
        return numero(tan($angulo));
    }
}
