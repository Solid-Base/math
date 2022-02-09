<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra\Decomposicao;

use ArithmeticError;
use DomainException;
use SolidBase\Matematica\Algebra\FabricaMatriz;
use SolidBase\Matematica\Algebra\Matriz;
use SolidBase\Matematica\Aritimetica\Numero;

class LU extends Decomposicao
{
    private int $trocas;
    private static int $trocasAux;
    private Numero $determinante;

    private function __construct(
        private Matriz $L,
        private Matriz $U,
        private Matriz $P
    ) {
    }

    public static function Decompor(Matriz $M): self
    {
        self::$trocasAux = 0;
        if (!$M->eQuadrada()) {
            throw new DomainException('Para fatoração LU, é necessário que a matriz seja de ordem quadrada.');
        }

        $n = $M->obtenhaN();
        $precisao = $M->obtenhaPrecisao();
        $U = (FabricaMatriz::Nula($n))->informarPrecisao($precisao)->obtenhaMatriz(false);
        $L = (FabricaMatriz::Diagonal(array_fill(0, $n, 1)))->informarPrecisao($precisao)->obtenhaMatriz(false);
        $P = self::pivotiar($M);
        $PA = $P->Multiplicar($M);

        for ($i = 0; $i < $n; ++$i) {
            for ($j = 0; $j <= $i; ++$j) {
                $soma = numero(0, $precisao ** 2);
                for ($k = 0; $k < $j; ++$k) {
                    $soma->somar(multiplicar($U[$k][$i], $L[$j][$k]));
                }
                $U[$j][$i] = subtrair($PA[$j][$i], $soma);
            }

            for ($j = $i; $j < $n; ++$j) {
                $soma = numero(0, $precisao ** 2);
                for ($k = 0; $k < $i; ++$k) {
                    $soma->somar(multiplicar($U[$k][$i], $L[$j][$k]));
                }
                $L[$j][$i] = (eZero($U[$i][$i])) ? NAN : (numero($PA[$j][$i], $precisao)->subtrair($soma)->dividir($U[$i][$i]));
            }
        }
        $L = FabricaMatriz::Criar($L)->informarPrecisao($precisao);
        $U = FabricaMatriz::Criar($U)->informarPrecisao($precisao);
        $retorno = new self($L, $U, $P);
        $retorno->trocas = self::$trocasAux;

        return $retorno;
    }

    public function offsetExists($i): bool
    {
        switch ($i) {
            case 'L':
            case 'U':
            case 'P':
                return true;

            default:
                return false;
        }
    }

    public function ResolverSistema(Matriz $B): Matriz
    {
        if (eZero($this->Determinante())) {
            throw new DomainException('O sistema não possui solução!');
        }

        $precisao = max($B->obtenhaPrecisao(), $this->L->obtenhaPrecisao());
        $L = $this->L;
        $U = $this->U;
        $P = $this->P;
        $m = $L->obtenhaM();
        $Pb = $P->Multiplicar($B);
        $y = [];
        $y[0] = dividir($Pb[0][0], $L[0][0]);
        for ($i = 1; $i < $m; ++$i) {
            $soma = numero(0, $precisao ** 2);
            for ($j = 0; $j <= $i - 1; ++$j) {
                $soma->somar(multiplicar($L[$i][$j], $y[$j]));
            }
            $y[$i] = subtrair($Pb[$i][0], $soma)->dividir($L[$i][$i]);
        }

        $x = [];
        $x[$m - 1] = dividir($y[$m - 1], $U[$m - 1][$m - 1]);
        for ($i = $m - 2; $i >= 0; --$i) {
            $soma = numero(0, $precisao ** 2);
            for ($j = $i + 1; $j < $m; ++$j) {
                $soma->somar(multiplicar($U[$i][$j], $x[$j]));
            }
            if (eZero($U[$i][$i])) {
                throw new ArithmeticError('Não é possível solucionar o sistema.');
            }
            $x[$i] = subtrair($y[$i], $soma)->dividir($U[$i][$i]);
        }

        return (FabricaMatriz::Criar(array_reverse($x)))->informarPrecisao($precisao);
    }

    public function Determinante($real = true): Numero|float
    {
        if (!empty($this->determinante)) {
            return $real ? $this->determinante->valor() : $this->determinante;
        }
        $u = $this->U;
        $det = numero(1, $u->obtenhaPrecisao() ** 2);
        $trocas = $this->trocas;
        for ($i = 0; $i < $u->obtenhaM(); ++$i) {
            $det = multiplicar($det, $u[$i][$i]);
        }

        $retorno = multiplicar($det, potencia(-1, $trocas));
        $this->determinante = eZero($retorno) ? numero(0, $u->obtenhaPrecisao()) : arredondar($retorno, $u->obtenhaPrecisao());

        return $real ? $this->determinante->valor() : $this->determinante;
    }

    protected static function Pivotiar(Matriz $A): Matriz
    {
        $n = $A->obtenhaN();
        $P = (FabricaMatriz::Identidade($n))->informarPrecisao($A->obtenhaPrecisao());
        for ($i = 0; $i < $n; ++$i) {
            $max = abs($A[$i][$i]->valor());
            $linha = $i;
            for ($j = $i; $j < $n; ++$j) {
                if (abs($A[$j][$i]->valor()) > $max) {
                    $max = abs($A[$j][$i]->valor());
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
