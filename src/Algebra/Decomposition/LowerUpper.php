<?php

declare(strict_types=1);

namespace SolidBase\Math\Algebra\Decomposition;

use ArithmeticError;
use DomainException;
use SolidBase\Math\Algebra\FabricaMatriz;
use SolidBase\Math\Algebra\Matriz;
use SolidBase\Math\Interfaces\Algebra\Decomposition\Decompose;
use SolidBase\Math\Interfaces\Algebra\Determinant;
use SolidBase\Math\Interfaces\Algebra\SolveSystem;

class LowerUpper implements Decompose, Determinant, SolveSystem
{
    private int $trocas;
    private static int $trocasAux;
    private float|int $determinante;

    private function __construct(
        private
        Matriz $L,
        private
        Matriz $U,
        private
        Matriz $P
    ) {}

    public static function Decompose(Matriz $M): LowerUpper
    {
        self::$trocasAux = 0;
        if (!$M->isSquare()) {
            throw new DomainException('Para fatoração LU, é necessário que a matriz seja de ordem quadrada.');
        }
        $n = $M->getN();
        $U = (FabricaMatriz::Zero($n))->getMatriz();
        $L = (FabricaMatriz::Diagonal(array_fill(0, $n, 1)))->getMatriz(false);
        $P = self::Pivot($M);
        $PA = $P->multiply($M);
        for ($i = 0; $i < $n; ++$i) {
            for ($j = 0; $j <= $i; ++$j) {
                $soma = 0;
                for ($k = 0; $k < $j; ++$k) {
                    $soma += $U[$k][$i] * $L[$j][$k];
                }
                $U[$j][$i] = $PA[$j][$i] - $soma;
            }

            for ($j = $i; $j < $n; ++$j) {
                $soma = 0;
                for ($k = 0; $k < $i; ++$k) {
                    $soma += $U[$k][$i] * $L[$j][$k];
                }
                $L[$j][$i] = (sbIsZero($U[$i][$i])) ? NAN : ($PA[$j][$i] - $soma) / $U[$i][$i];
            }
        }
        $L = FabricaMatriz::Create($L);
        $U = FabricaMatriz::Create($U);
        $retorno = new static ($L, $U, $P);
        $retorno->trocas = self::$trocasAux;

        return $retorno;
    }

    public function SolveSystem(Matriz $B): Matriz
    {
        if (sbIsZero($this->Determinant())) {
            throw new DomainException('O sistema não possui solução!');
        }

        $L = $this->L;
        $U = $this->U;
        $P = $this->P;
        $m = $L->getM();
        $Pb = $P->multiply($B);
        $y = [];
        $y[0] = (float)$Pb[0] / $L[0][0];
        for ($i = 1; $i < $m; ++$i) {
            $soma = 0;
            for ($j = 0; $j <= $i - 1; ++$j) {
                $soma += $L[$i][$j] * $y[$j];
            }
            $y[$i] = ((float)$Pb[$i] - $soma) / $L[$i][$i];
        }

        $x = [];
        $x[$m - 1] = $y[$m - 1] / $U[$m - 1][$m - 1];
        for ($i = $m - 2; $i >= 0; --$i) {
            $soma = 0;
            for ($j = $i + 1; $j < $m; ++$j) {
                $soma += $U[$i][$j] * $x[$j];
            }
            // if (eZero($U[$i][$i])) {
            //     throw new ArithmeticError('Não é possível solucionar o sistema.');
            // }
            $x[$i] = ($y[$i] - $soma) / ($U[$i][$i]);
        }

        return FabricaMatriz::Create(array_reverse($x));
    }

    public function Determinant(): float
    {
        if (!empty($this->determinante)) {
            return $this->determinante;
        }
        $u = $this->U;
        $det = 1;
        $trocas = $this->trocas;
        for ($i = 0; $i < $u->getM(); ++$i) {
            $det = $det * $u[$i][$i];
        }
        $retorno = $det * ((-1) ** $trocas);
        $this->determinante = is_nan($retorno) ? 0 : sbNormalize($retorno);

        return $this->determinante;
    }

    protected static function Pivot(Matriz $A): Matriz
    {
        $n = $A->getN();
        $P = (FabricaMatriz::Identity($n));
        for ($i = 0; $i < $n; ++$i) {
            $max = abs($A[$i][$i]);
            $linha = $i;
            for ($j = $i; $j < $n; ++$j) {
                if (abs($A[$j][$i]) > $max) {
                    $max = abs($A[$j][$i]);
                    $linha = $j;
                }
            }
            if ($i !== $linha) {
                ++self::$trocasAux;
                $P->switchRow($i, $linha);
            }
        }

        return $P;
    }
}
