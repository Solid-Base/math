<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra;

use Solidbase\Geometria\Dominio\Plano;
use Solidbase\Geometria\Dominio\Ponto;
use Solidbase\Geometria\Dominio\Vetor;

class FabricaMatriz
{
    private function __construct()
    {
    }

    public static function Criar(array $matriz): Matriz
    {
        return new Matriz($matriz);
    }

    public static function Identidade(int $n): Matriz
    {
        $matriz = [];
        for ($i = 0; $i < $n; ++$i) {
            for ($j = 0; $j < $n; ++$j) {
                $matriz[$i][$j] = $i === $j ? 1 : 0;
            }
        }

        return new Matriz($matriz);
    }

    public static function Nula(int $n): Matriz
    {
        $matriz = [];
        for ($i = 0; $i < $n; ++$i) {
            $linha = array_fill(0, $n, 0);
            $matriz[$i] = $linha;
        }

        return new Matriz($matriz);
    }

    public static function Diagonal(array $diagonal): Matriz
    {
        $n = \count($diagonal);
        $matriz = [];
        for ($i = 0; $i < $n; ++$i) {
            for ($j = 0; $j < $n; ++$j) {
                if ($i === $j) {
                    $matriz[$i][$j] = $diagonal[$i];

                    continue;
                }

                $matriz[$i][$j] = 0;
            }
        }

        return new Matriz($matriz);
    }

    public static function MatrizRotacao(Vetor $eixo, float $angulo): Matriz
    {
        $retorno = self::Nula(3)->obtenhaMatriz();
        $cos = cos($angulo);
        $sen = sin($angulo);
        $unitario = $eixo->VetorUnitario();
        $umCos = 1 - $cos;
        $retorno[0][0] = ($unitario->x ** 2) * $umCos + $cos;
        $retorno[0][1] = ($unitario->x * $unitario->y) * $umCos - $unitario->z * $sen;
        $retorno[0][2] = ($unitario->x * $unitario->z) * $umCos + $unitario->y * $sen;

        $retorno[1][0] = ($unitario->x * $unitario->y) * $umCos + $unitario->z * $sen;
        $retorno[1][1] = ($unitario->y ** 2) * $umCos + $cos;
        $retorno[1][2] = ($unitario->y * $unitario->z) * $umCos - $unitario->x * $sen;

        $retorno[2][0] = ($unitario->x * $unitario->z) * $umCos - $unitario->y * $sen;
        $retorno[2][1] = ($unitario->y * $unitario->z) * $umCos + $unitario->x * $sen;
        $retorno[2][2] = ($unitario->z ** 2) * $umCos + $cos;

        return new Matriz($retorno);
    }

    public static function Reflexao(Plano $plano): Matriz
    {
        $normal = $plano->normal;
        $m00 = 1 - 2 * ($normal->x ** 2);
        $m01 = -2 * ($normal->x * $normal->y);
        $m02 = -2 * $normal->x * $normal->z;

        $m10 = -2 * ($normal->x * $normal->y);
        $m11 = 1 - 2 * ($normal->y ** 2);
        $m12 = -2 * $normal->y * $normal->z;

        $m20 = -2 * ($normal->x * $normal->z);
        $m21 = -2 * ($normal->y * $normal->z);
        $m22 = 1 - 2 * ($normal->z ** 2);

        $matriz = [[$m00, $m01, $m02], [$m10, $m11, $m12], [$m20, $m21, $m22]];

        return new Matriz($matriz);
    }

    public static function MatrizPonto(Ponto $ponto): Matriz
    {
        $matriz = [[$ponto->x], [$ponto->y], [$ponto->z]];

        return new Matriz($matriz);
    }
}
