<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra\Decomposicao;

use ArithmeticError;
use DomainException;
use SolidBase\Matematica\Algebra\FabricaMatriz;
use SolidBase\Matematica\Algebra\Matriz;

class DecomposicaoLU
{
    private int $trocas;
    private static int $trocasAux;
    private float|int $determinante;

    private function __construct(
        private Matriz $L,
        private Matriz $U,
        private Matriz $P
    ) {
    }

    public static function Decompor(Matriz $M): static
    {
        self::$trocasAux = 0;
        if (!$M->eMatrizQuadrada()) {
            throw new DomainException('Para fatoração LU, é necessário que a matriz seja de ordem quadrada.');
        }

        $n = $M->numeroColuna();
        $U = FabricaMatriz::Nula($n);
        $L = FabricaMatriz::Diagonal((array_fill(0, $n, 1)));
        $P = self::pivotiar($M);
        $PA = $P->multiplicar($M);

        for ($i = 1; $i <= $n; ++$i) {
            for ($j = 1; $j <= $i; ++$j) {
                $soma = 0;
                for ($k = 1; $k < $j; ++$k) {
                    $soma += $U["{$k},{$i}"] * $L["{$j},{$k}"];
                }
                $U["{$j},{$i}"] = $PA["{$j},{$i}"] - $soma;
            }

            for ($j = $i; $j <= $n; ++$j) {
                $soma = 0;
                for ($k = 1; $k < $i; ++$k) {
                    $soma += $U["{$k},{$i}"] * $L["{$j},{$k}"];
                }
                $L["{$j},{$i}"] = (eZero($U["{$i},{$i}"])) ? NAN : ($PA["{$j},{$i}"] - $soma) / $U["{$i},{$i}"];
            }
        }
        $retorno = new static($L, $U, $P);
        $retorno->trocas = self::$trocasAux;

        return $retorno;
    }

    public function Determinante(): float
    {
        if (!empty($this->determinante)) {
            return $this->determinante;
        }
        $u = $this->U;
        $det = 1;
        $trocas = $this->trocas;
        for ($i = 1; $i <= $u->numeroColuna(); ++$i) {
            $det = $det * $u["{$i},{$i}"];
        }

        $retorno = $det * ((-1) ** $trocas);
        $this->determinante = normalizar($retorno);

        return $this->determinante;
    }

    public function ResolverSistema(Matriz $B): Matriz
    {
        if (!$B->eMatrizColuna()) {
            throw new DomainException('A matriz B, deve ser do tipo coluna');
        }
        if (eZero($this->Determinante())) {
            throw new DomainException('O sistema não possui solução!');
        }

        $L = $this->L;
        $U = $this->U;
        $P = $this->P;
        $m = $L->numeroColuna();
        $Pb = $P->multiplicar($B);
        $y = [];
        $y[1] = $Pb['1'] / $L['1,1'];
        for ($i = 2; $i <= $m; ++$i) {
            $soma = 0;
            for ($j = 1; $j <= $i - 1; ++$j) {
                $soma += $L["{$i},{$j}"] * $y[$j];
            }
            $y[$i] = ($Pb["{$i}"] - $soma) / $L["{$i},{$i}"];
        }

        $x = [];

        $x[$m] = [$y[$m] / $U["{$m},{$m}"]];
        for ($i = $m - 1; $i >= 1; --$i) {
            $soma = 0;
            for ($j = $i + 1; $j <= $m; ++$j) {
                $soma += $U["{$i},{$j}"] * $x[$j][0];
            }
            if (eZero($U["{$i},{$i}"])) {
                throw new ArithmeticError('Não é possível solucionar o sistema.');
            }
            $x[$i] = [($y[$i] - $soma) / ($U["{$i},{$i}"])];
        }

        return new Matriz(array_reverse($x));
    }

    protected static function Pivotiar(Matriz $A): Matriz
    {
        $n = $A->numeroColuna();
        $P = FabricaMatriz::Identidade($n);
        for ($i = 1; $i <= $n; ++$i) {
            $max = abs($A["{$i},{$i}"]);
            $linha = $i;
            for ($j = $i; $j <= $n; ++$j) {
                if (eMaior(abs($A["{$j},{$i}"]), $max)) {
                    $max = abs($A["{$j},{$i}"]);
                    $linha = $j;
                }
            }
            if ($i !== $linha) {
                ++self::$trocasAux;
                $P->trocarLinha($i, $linha);
            }
        }

        return $P;
    }
}
