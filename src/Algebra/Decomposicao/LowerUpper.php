<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra\Decomposicao;

use ArithmeticError;
use DomainException;
use SolidBase\Matematica\Algebra\FabricaMatriz;
use SolidBase\Matematica\Interfaces\Algebra\Decomposicao\IDecomposicao;
use SolidBase\Matematica\Interfaces\Algebra\IMatriz;
use SolidBase\Matematica\Interfaces\IDeterminante;
use SolidBase\Matematica\Interfaces\IResolverSistema;

class LowerUpper implements IDecomposicao, IDeterminante, IResolverSistema
{
    private int $trocas;
    private static int $trocasAux;
    private float|int $determinante;

    private function __construct(
        private
        IMatriz $L,
        private
        IMatriz $U,
        private
        IMatriz $P
    ) {
    }

    public static function Decompor(IMatriz $M): LowerUpper
    {
        self::$trocasAux = 0;
        if (!$M->eQuadrada()) {
            throw new DomainException('Para fatoração LU, é necessário que a matriz seja de ordem quadrada.');
        }

        $n = $M->obtenhaN();
        $U = (FabricaMatriz::Nula($n))->obtenhaMatriz();
        $L = (FabricaMatriz::Diagonal(array_fill(0, $n, 1)))->obtenhaMatriz(false);
        $P = self::pivotiar($M);
        $PA = $P->Multiplicar($M);

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
                $L[$j][$i] = (eZero($U[$i][$i])) ? NAN : ($PA[$j][$i] - $soma) / $U[$i][$i];
            }
        }
        $L = FabricaMatriz::Criar($L);
        $U = FabricaMatriz::Criar($U);
        $retorno = new static ($L, $U, $P);
        $retorno->trocas = self::$trocasAux;

        return $retorno;
    }

    public function ResolverSistema(IMatriz $B): IMatriz
    {
        if (eZero($this->Determinante())) {
            throw new DomainException('O sistema não possui solução!');
        }

        $L = $this->L;
        $U = $this->U;
        $P = $this->P;
        $m = $L->obtenhaM();
        $Pb = $P->Multiplicar($B);
        $y = [];
        $y[0] = $Pb[0] / $L[0][0];
        for ($i = 1; $i < $m; ++$i) {
            $soma = 0;
            for ($j = 0; $j <= $i - 1; ++$j) {
                $soma += $L[$i][$j] * $y[$j];
            }
            $y[$i] = ($Pb[$i] - $soma) / $L[$i][$i];
        }

        $x = [];
        $x[$m - 1] = $y[$m - 1] / $U[$m - 1][$m - 1];
        for ($i = $m - 2; $i >= 0; --$i) {
            $soma = 0;
            for ($j = $i + 1; $j < $m; ++$j) {
                $soma += $U[$i][$j] * $x[$j];
            }
            if (eZero($U[$i][$i])) {
                throw new ArithmeticError('Não é possível solucionar o sistema.');
            }
            $x[$i] = ($y[$i] - $soma) / ($U[$i][$i]);
        }

        return FabricaMatriz::Criar(array_reverse($x));
    }

    public function Determinante(): float
    {
        if (!empty($this->determinante)) {
            return $this->determinante;
        }
        $u = $this->U;
        $det = 1;
        $trocas = $this->trocas;
        for ($i = 0; $i < $u->obtenhaM(); ++$i) {
            $det = $det * $u[$i][$i];
        }

        $retorno = $det * ((-1) ** $trocas);
        $this->determinante = normalizar($retorno);

        return $this->determinante;
    }

    protected static function Pivotiar(IMatriz $A): IMatriz
    {
        $n = $A->obtenhaN();
        $P = (FabricaMatriz::Identidade($n));
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
                $P->trocarLinha($i, $linha);
            }
        }

        return $P;
    }
}
