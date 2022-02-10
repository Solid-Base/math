<?php

declare(strict_types=1);

use SolidBase\Matematica\Aritimetica\Numero;

if (!defined('PRECISAO_SOLIDBASE')) {
    $scale = 0 === bcscale() ? 15 : bcscale();
    define('PRECISAO_SOLIDBASE', $scale);
    bcscale($scale);
}

if (!function_exists('somar')) {
    function somar(int|string|float|Numero $valor1, int|string|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->somar($valor2);
    }
}
if (!function_exists('numero')) {
    function numero(int|float|string|Numero $valor, ?int $precisao = null): Numero
    {
        $precisao ??= is_a($valor, Numero::class) ? $valor->precisao : $precisao;

        return is_a($valor, Numero::class) ? new Numero((string) $valor, $precisao) : new Numero($valor, $precisao);
    }
}

if (!function_exists('subtrair')) {
    function subtrair(int|string|float|Numero $valor1, int|string|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->subtrair($valor2);
    }
}

if (!function_exists('multiplicar')) {
    function multiplicar(int|string|float|Numero $valor1, int|string|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->multiplicar($valor2);
    }
}

if (!function_exists('dividir')) {
    function dividir(int|string|float|Numero $valor1, int|string|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->dividir($valor2);
    }
}

if (!function_exists('eInteiro')) {
    function eInteiro(int|string|float|Numero $valor): bool
    {
        $numeroInteiro = numero($valor)->arredondar(0);

        return eIgual($valor, $numeroInteiro, false);
    }
}
if (!function_exists('modulo')) {
    function modulo(int|string|float|Numero $valor): Numero
    {
        $numero = numero($valor);

        return $numero->modulo();
    }
}
if (!function_exists('mod')) {
    function mod(int|string|float|Numero $valor1, int|string|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->mod($valor2);
    }
}

if (!function_exists('eIgual')) {
    function eIgual(int|string|float|Numero $valor1, int|string|float|Numero $valor2, $estrito = true): bool
    {
        $numero = numero($valor1);
        if ($estrito) {
            return $numero->eIgual($valor2);
        }

        return eZero(subtrair($numero, $valor2));
    }
}

if (!function_exists('eZero')) {
    function eZero(int|string|float|Numero $valor): bool
    {
        $precisao = is_a($valor, Numero::class) ? $valor->precisao : bcscale();
        $zeroEsquerda = numero(ZERO_SOLIDBASE)->multiplicar(-1);
        $zeroDireita = numero(ZERO_SOLIDBASE);

        return entre($zeroEsquerda, arredondar($valor, $precisao), $zeroDireita);
    }
}
if (!function_exists('arredondar')) {
    function arredondar(int|float|string|Numero $numero, int $casas): Numero
    {
        $numero = numero($numero);

        return $numero->arredondar($casas);
    }
}
if (!function_exists('entre')) {
    function entre(
        int|string|float|Numero $valorEsquerda,
        int|string|float|Numero $valorComparacao,
        int|string|float|Numero $valorDireita
    ): bool {
        $valorEsquerda = numero($valorEsquerda);
        $valorDireita = numero($valorDireita);
        $valorComparacao = numero($valorComparacao);

        if (eMaior($valorEsquerda, $valorComparacao)) {
            return false;
        }
        if (eMenor($valorDireita, $valorComparacao)) {
            return false;
        }

        return true;
    }
}

if (!function_exists('potencia')) {
    function potencia(int|float|string|Numero $valor1, int|Numero $valor2): Numero
    {
        $pontecia = arredondar($valor2, 0)->valor();
        $numero = numero($valor1);

        return $numero->potencia($pontecia);
    }
}

if (!function_exists('eMenor')) {
    function eMenor(int|float|string|Numero $valor1, int|float|string|Numero $valor2): bool
    {
        $numero = numero($valor1);

        return $numero->eMenor($valor2);
    }
}

if (!function_exists('eMaior')) {
    function eMaior(int|float|string|Numero $valor1, int|float|string|Numero $valor2): bool
    {
        $numero = numero($valor1);

        return $numero->eMaior($valor2);
    }
}

if (!function_exists('comparar')) {
    function comparar(int|float|string|Numero $valor1, int|float|string|Numero $valor2): int
    {
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

        return $numero->comparar($valor2);
    }
}

if (!function_exists('fatorial')) {
    function fatorial(int|string|Numero $numero): Numero
    {
        $fatorial = numero(arredondar($numero, 0));

        while ($numero > 1) {
            $fatorial->multiplicar(--$numero);
        }

        return $fatorial;
    }
}

if (!function_exists('raiz')) {
    function raiz(float|int|string|Numero $numero): Numero
    {
        $numero = numero($numero);
        if (eMenor($numero, 0)) {
            throw new DomainException('Não é possível tirar raiz de numero negativo');
        }

        return $numero->raiz();
    }
}

if (!defined('ZERO_SOLIDBASE')) {
    $scale = bcscale();
    $precisao = (int) max($scale / 2, 9);
    $zero = dividir(numero(1, $precisao), potencia(10, $precisao));
    define('ZERO_SOLIDBASE', $zero);
}
