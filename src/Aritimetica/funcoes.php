<?php

declare(strict_types=1);

use SolidBase\Matematica\Aritimetica\Numero;

if (!function_exists('somar')) {
    function somar(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

        return $numero->somar($valor2);
    }
}

if (!function_exists('subtrair')) {
    function subtrair(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

        return $numero->subtrair($valor2);
    }
}

if (!function_exists('multiplicar')) {
    function multiplicar(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

        return $numero->multiplicar($valor2);
    }
}

if (!function_exists('dividir')) {
    function dividir(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

        return $numero->dividir($valor2);
    }
}
if (!function_exists('modulo')) {
    function modulo(int|float|Numero $valor): Numero
    {
        $numero = is_a($valor, Numero::class) ? $valor : new Numero($valor);

        return $numero->modulo();
    }
}
if (!function_exists('mod')) {
    function mod(int|float|Numero $valor1, int|float|Numero $valor2): Numero
    {
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

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
        $numero = is_a($valor1, Numero::class) ? $valor1 : new Numero($valor1);

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
        $fatorial = is_a($numero, Numero::class) ? $numero->subtrair(1) : new Numero($numero--);

        while ($numero > 1) {
            $fatorial = $fatorial->multiplicar($numero--);
        }

        return $fatorial;
    }
}

if (!function_exists('seno')) {
    function seno(float $angulo): Numero
    {
        $escala = bcscale();
        if ($escala < 35) {
            bcscale(35);
        }
        $or = $angulo;
        $r = subtrair($angulo, dividir(potencia($angulo, 3), 6));
        $i = 2;
        while (subtrair($or, $r)->modulo()->eMaior(1E-100)) {
            $or = $r;

            switch ($i % 2) {
                case 0:
                    $r = somar($r, dividir(potencia($angulo, $i * 2 + 1), fatorial($i * 2 + 1)));

                    break;

                    default:
                    $r = subtrair($r, dividir(potencia($angulo, $i * 2 + 1), fatorial($i * 2 + 1)));

                    break;
            }
            ++$i;
        }

        bcscale($escala);
        if ($r->modulo()->eMenor(1E-10)) {
            return new Numero(0);
        }

        return $r->arredondar(20);
    }
}

if (!function_exists('cosseno')) {
    function cosseno(float $angulo): Numero
    {
        $escala = bcscale();
        if ($escala < 35) {
            bcscale(35);
        }
        $or = $angulo;
        $r = subtrair(1, dividir(potencia($angulo, 2), 2));
        $i = 2;
        while (subtrair($or, $r)->modulo()->eMaior(1E-100)) {
            $or = $r;

            switch ($i % 2) {
                case 0:
                    $r = somar($r, dividir(potencia($angulo, $i * 2), fatorial($i * 2)));

                    break;

                    default:
                    $r = subtrair($r, dividir(potencia($angulo, $i * 2), fatorial($i * 2)));

                    break;
            }
            ++$i;
        }

        bcscale($escala);
        if ($r->modulo()->eMenor(1E-10)) {
            return new Numero(0);
        }

        return $r->arredondar(20);
    }
}

if (!function_exists('tangente')) {
    function tangente(float|Numero $angulo): Numero
    {
        $numero = is_a($angulo, Numero::class) ? $angulo : new Numero($angulo);
        if ($numero->eIgual(M_PI_2) || $numero->eIgual(M_PI_2 * 2)) {
            return new Numero(1.6331239353195E+16);
        }
        $escala = bcscale();
        if ($escala < 35) {
            bcscale(35);
        }

        $r = seno($angulo)->dividir(cosseno($angulo));
        bcscale($escala);
        if ($r->modulo()->eMenor(1E-10)) {
            return new Numero(0);
        }

        return $r->arredondar(20);
    }
}
