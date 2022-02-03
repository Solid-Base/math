<?php

declare(strict_types=1);

use SolidBase\Matematica\Aritimetica\Numero;

if (!defined('PRECISAO_SOLIDBASE')) {
    $scale = 0 === bcscale() ? 12 : bcscale();
    define('PRECISAO_SOLIDBASE', $scale);
    bcscale($scale);
}

if (!defined('ZERO_SOLIDBASE')) {
    $scale = bcscale();
    $precisao = (int) min($scale, 9);
    define('ZERO_SOLIDBASE', 1 / (10 ** $precisao));
}

if (!function_exists('somar')) {
    function somar(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->somar($valor2);
    }
}
if (!function_exists('numero')) {
    function numero(int|float|string|Numero $valor): Numero
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
        return entre(-ZERO_SOLIDBASE, $valor, ZERO_SOLIDBASE);
    }
}
if (!function_exists('arredondar')) {
    function arredondar(int|float|Numero $numero, int $casas): float
    {
        $numero = numero($numero);
        $numero = $numero->arredondar($casas);

        return $numero->valor();
    }
}
if (!function_exists('entre')) {
    function entre(
        int|float|Numero $valorEsquerda,
        int|float|Numero $valorComparacao,
        int|float|Numero $valorDireita
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
    function potencia(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = numero($valor1);

        return $numero->potencia($valor2);
    }
}

if (!function_exists('eMenor')) {
    function eMenor(int|float|Numero $valor1, int|float|Numero $valor2): bool
    {
        $numero = numero($valor1);

        return $numero->eMenor($valor2);
    }
}

if (!function_exists('eMaior')) {
    function eMaior(int|float|Numero $valor1, int|float|Numero $valor2): bool
    {
        $numero = numero($valor1);

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

if (!function_exists('raiz')) {
    function raiz(float|int|Numero $numero): Numero
    {
        $numero = numero($numero);

        return $numero->raiz();
    }
}

if (!function_exists('seno')) {
    function seno(int|float|Numero $angulo): Numero
    {
        $scale = bcscale();
        bcscale($scale + 12);
        $or = numero($angulo);
        $r = subtrair($angulo, dividir(potencia($angulo, 3), 6));
        $i = 2;
        while (comparar($or, $r)) {
            $or = $r;

            switch ($i % 2) {
              case 0:  $r = somar($r, dividir(potencia($angulo, $i * 2 + 1), fatorial($i * 2 + 1)));

              break;

              default: $r = subtrair($r, dividir(potencia($angulo, $i * 2 + 1), fatorial($i * 2 + 1)));

              break;
            }
            ++$i;
        }

        $retorno = $r->arredondar($scale - 1);
        bcscale($scale);

        return $retorno;
    }
}

if (!function_exists('cosseno')) {
    function cosseno(int|float|Numero $angulo): Numero
    {
        $scale = bcscale();
        bcscale($scale + 12);

        $or = numero($angulo);
        $r = subtrair(1, dividir(potencia($angulo, 2), 2));
        $i = 2;
        while (comparar($or, $r)) {
            $or = $r;

            switch ($i % 2) {
          case 0:  $r = somar($r, dividir(potencia($angulo, $i * 2), fatorial($i * 2)));

          break;

          default: $r = subtrair($r, dividir(potencia($angulo, $i * 2), fatorial($i * 2)));

          break;
        }
            ++$i;
        }

        $retorno = $r->arredondar($scale - 1);
        bcscale($scale);

        return $retorno;
    }
}

if (!function_exists('tangente')) {
    function tangente(int|float|Numero $angulo): Numero
    {
        $scale = bcscale();
        bcscale($scale + 12);
        $cosseno = cosseno($angulo);
        if (eZero($cosseno)) {
            bcscale($scale);

            return numero(1E1000);
        }
        $seno = seno($angulo);
        $tangente = dividir($seno, $cosseno);
        $retorno = $tangente->arredondar($scale - 1);
        bcscale($scale);

        return $retorno;
    }
}

if (!function_exists('sbPi')) {
    function sbPi(int|Numero $precisao): Numero
    {
        $precision = is_int($precisao) ? $precisao : $precisao->arredondar(0)->valor();
        $limit = ceil(log($precision) / log(2)) - 1;
        $scale = bcscale();
        bcscale($precision + 6);
        $a = numero(1);
        $b = dividir(1, raiz(2));
        $t = dividir(1, 4);
        $p = numero(1);
        $n = 0;
        while ($n < $limit) {
            $x = dividir(somar($a, $b), 2);
            $y = raiz(multiplicar($a, $b));
            $t = subtrair($t, multiplicar($p, potencia(subtrair($a, $x), 2)));
            $a = $x;
            $b = $y;
            $p = multiplicar(2, $p);
            ++$n;
        }

        $valor = dividir(potencia(somar($a, $b), 2), multiplicar(4, $t))->arredondar($precision);
        bcscale($scale);

        return $valor;
    }
}

if (!defined('S_PI')) {
    $pi = (string) sbPi(5 * PRECISAO_SOLIDBASE);
    define('S_PI', $pi);
}

if (!function_exists('radiano')) {
    function radiano(int|float|string|Numero $anguloGrau): Numero
    {
        $scale = bcscale();
        bcscale(5 * PRECISAO_SOLIDBASE);
        $pi = numero(S_PI);
        $grau = numero($anguloGrau);
        $retorno = (multiplicar($grau, $pi))->dividir(180);
        bcscale($scale);

        return $retorno;
    }
}

if (!function_exists('grau')) {
    function grau(int|float|string|Numero $anguloRad): Numero
    {
        $scale = bcscale();
        bcscale(5 * PRECISAO_SOLIDBASE);
        $pi = numero(S_PI);
        $rad = numero($anguloRad);
        $retorno = (multiplicar($rad, 180))->dividir($pi);
        bcscale($scale);

        return $retorno;
    }
}
