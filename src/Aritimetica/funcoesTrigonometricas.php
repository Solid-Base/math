<?php

declare(strict_types=1);

use SolidBase\Matematica\Aritimetica\Numero;

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

if (!function_exists('arcoSeno')) {
    function arcoSeno(int|string|float|Numero $seno): Numero
    {
        if (eMaior($seno, 1) || eMenor($seno, -1)) {
            throw new DomainException('Função Arco Seno, deve ter numeros no intervalo fechado -1 e 1');
        }
        if (eIgual($seno, 1)) {
            return dividir(S_PI, 2);
        }
        if (eIgual($seno, -1)) {
            return dividir(S_PI, 2)->multiplicar(-1);
        }
        $seno = numero($seno);
        $precisao = $seno->precisao + 12;
        $or = numero($seno, $precisao);
        $r = somar($or, dividir(multiplicar(numero(2, $precisao), potencia($seno, 3)), numero(12, $precisao)));
        $i = 2;
        while (comparar($or, $r)) {
            $or = $r;

            $rS = multiplicar(numero(fatorial(2 * $i), $precisao), potencia($seno, 2 * $i + 1));
            $rD = multiplicar(multiplicar(potencia(numero(4, $precisao), $i), potencia(fatorial($i), 2)), 2 * $i + 1);
            $r = somar($r, dividir($rS, $rD));

            ++$i;
        }

        return $r->arredondar($precisao - 12);
    }
}
if (!function_exists('arcoCosseno')) {
    function arcoCosseno(int|string|float|Numero $cosseno): Numero
    {
        if (eMaior($cosseno, 1) || eMenor($cosseno, -1)) {
            throw new DomainException('Função Arco Cosseno, deve ter numeros no intervalo fechado -1 e 1');
        }
        $cosseno = numero($cosseno);
        $seno = subtrair(numero(1, $cosseno->precisao), potencia($cosseno, 2))->raiz();
        $arcoSeno = arcoSeno($seno);
        if (eMenor($cosseno, 0)) {
            return subtrair(S_PI, $arcoSeno);
        }

        return $arcoSeno;
    }
}

if (!function_exists('tangente')) {
    function tangente(int|string|float|Numero $angulo): Numero
    {
        $cosseno = cosseno($angulo);
        if (eZero($cosseno)) {
            throw new DomainException("A tangente do angulo {$angulo} é indefinida");
        }
        $seno = seno($angulo);

        return dividir($seno, $cosseno);
    }
}

if (!function_exists('arcoTangente')) {
    function arcoTangente(int|string|float|Numero $tangente): Numero
    {
        $tangente = numero($tangente);
        $precisao = $tangente->precisao + 12;
        $or = numero($tangente, $precisao);
        $r = subtrair($or, dividir(potencia($tangente, 3), numero(3, $precisao)));
        $i = 2;
        while (comparar($or, $r)) {
            $or = $r;

            switch ($i % 2) {
              case 0:  $r = somar($r, dividir(potencia($tangente, $i * 2 + 1), ($i * 2 + 1)));

              break;

              default: $r = subtrair($r, dividir(potencia($tangente, $i * 2 + 1), ($i * 2 + 1)));

              break;
            }
            ++$i;
        }

        return $r->arredondar($precisao - 12);
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
