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
        $numeroAbaixo = numero($valor);
        $numeroAcima = numero($valor);
        $valorAbaixo = $numeroAbaixo->InteiroAbaixo();
        $valorAcima = $numeroAcima->InteiroAcima();

        return eZero($numeroAbaixo->subtrair($valorAbaixo)) || eZero($numeroAcima->subtrair($valorAcima));
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
    function eIgual(int|string|float|Numero $valor1, int|string|float|Numero $valor2): bool
    {
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

        return $numero->eIgual($valor2);
    }
}

if (!function_exists('eZero')) {
    function eZero(int|string|float|Numero $valor): bool
    {
        $precisao = is_a($valor, Numero::class) ? $valor->precisao : bcscale();

        return entre(-ZERO_SOLIDBASE, arredondar($valor, $precisao), ZERO_SOLIDBASE);
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

if (!function_exists('seno')) {
    function seno(int|string|float|Numero $angulo): Numero
    {
        $angulo = numero($angulo);
        $precisao = $angulo->precisao + 12;
        $or = numero($angulo, $precisao);
        $r = subtrair($or, dividir(potencia($angulo, 3), numero(6, $precisao)));
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

        return $r->arredondar($precisao - 12);
    }
}

if (!function_exists('cosseno')) {
    function cosseno(int|string|float|Numero $angulo): Numero
    {
        $angulo = numero($angulo);
        $precisao = $angulo->precisao + 12;
        $or = numero($angulo);
        $r = subtrair(numero(1, $precisao), dividir(potencia($angulo, 2), numero(2, $precisao)));
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

        return $r->arredondar($precisao - 12);
    }
}

if (!function_exists('tangente')) {
    function tangente(int|string|float|Numero $angulo): Numero
    {
        $cosseno = cosseno($angulo);
        if (eZero($cosseno)) {
            return numero(1E20);
        }
        $seno = seno($angulo);

        return dividir($seno, $cosseno);
    }
}

if (!function_exists('sbPi')) {
    function sbPi(int|Numero $precisao): Numero
    {
        $precision = (int) arredondar($precisao, 0)->valor();
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
    $pi = (string) sbPi(2 * PRECISAO_SOLIDBASE);
    define('S_PI', $pi);
}

if (!function_exists('radiano')) {
    function radiano(int|float|string|Numero $anguloGrau): Numero
    {
        $pi = numero(S_PI);
        $grau = numero($anguloGrau, $pi->precisao);

        return (multiplicar($pi, $grau))->dividir(180);
    }
}

if (!function_exists('grau')) {
    function grau(int|float|string|Numero $anguloRad): Numero
    {
        $pi = numero(S_PI);
        $rad = numero($anguloRad, $pi->precisao);

        return (multiplicar($rad, 180))->dividir($pi);
    }
}
