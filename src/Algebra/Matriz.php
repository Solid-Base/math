<?php

declare(strict_types=1);

namespace SolidBase\Math\Algebra;

use ArrayAccess;
use Countable;
use DomainException;
use InvalidArgumentException;
use SolidBase\Math\Exception\ColumnDoesNotExistException;

class Matriz implements ArrayAccess, Countable
{
    public function __construct(array |Matriz $matriz)
    {
        $matriz = is_a($matriz, Matriz::class) ? $matriz->getMatriz() : $matriz;
        $this->numberOfRow = \count($matriz);
        $this->numberOfCol = \is_array($matriz[0]) ? \count($matriz[0]) : 1;
        foreach ($matriz as $i => $linha) {
            if (!\is_array($linha)) {
                $this->adicionarItem($i, 0, $linha);

                continue;
            }
            foreach ($linha as $j => $valor) {
                $this->adicionarItem($i, $j, $valor);
            }
        }
    }
    protected array $matriz = [];

    protected int $numberOfRow;
    protected int $numberOfCol;

    public function getMatriz(): array
    {
        $retorno = [];
        if (1 == $this->numberOfRow) {
            return array_map(fn(float|int $n) => sbNormalize($n), $this->matriz[0]);
        }
        foreach ($this->matriz as $linha => $valores) {
            foreach ($valores as $coluna => $valor) {
                $retorno[$linha][$coluna] = sbNormalize($valor);
            }
        }

        return $retorno;
    }

    public function getM(): int
    {
        return $this->numberOfRow;
    }

    public function getN(): int
    {
        return $this->numberOfCol;
    }

    public function isSquare(): bool
    {
        return $this->numberOfCol === $this->numberOfRow;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->matriz[$offset]);
    }

    public function offsetSet($offset, $value): void
    {
        throw new DomainException('Não é possível setar valores isolados na matriz');
    }

    public function offsetUnset($offset): void
    {
        throw new DomainException('Não é possível apagar valores isolados da matriz');
    }

    public function jsonSerialize(): mixed
    {
        return $this->matriz;
    }

    public function count(): int
    {
        return count($this->matriz);
    }

    public function offsetGet($offset): Matriz|float
    {
        if (1 == $this->numberOfRow) {
            return $this->getCol($offset)->getMatriz()[0];
        }
        if (1 == $this->numberOfCol) {
            return $this->getRow($offset)->getMatriz()[0];
        }
        return $this->getRow($offset);
    }

    public function addCol(array $coluna): void
    {
        if (\count($coluna) != $this->numberOfRow) {
            throw new DomainException('Para adicionar uma coluna, a mesma deve ter o mesmo numero de linhas que a matriz');
        }
        $j = $this->numberOfCol;
        for ($i = 0; $i < \count($coluna); ++$i) {
            $this->adicionarItem($i, $j, $coluna[$i]);
        }
        ++$this->numberOfCol;
    }

    public function addRow(array $linha): void
    {
        if (\count($linha) != $this->numberOfCol) {
            throw new DomainException('Para adicionar uma linha, a mesma deve ter o mesmo numero de colunas que a matriz');
        }
        $this->matriz[$this->numberOfRow] = array_map(fn(float|int $n) => sbNormalize($n), $linha);
        ++$this->numberOfRow;
    }

    public function item(int $i, int $j): float|int
    {
        if (!isset($this->matriz[$i]) || !isset($this->matriz[$i][$j])) {
            throw new InvalidArgumentException('Não existe o item solicitado');
        }

        return $this->matriz[$i][$j];
    }

    public function getRow(int $i): Matriz
    {
        $matriz = [0 => $this->matriz[$i]];

        return new Matriz($matriz);
    }

    public function getCol(int $j): Matriz
    {
        if ($j >= $this->numberOfCol) {
            throw new ColumnDoesNotExistException();
        }
        $retorno = [];
        for ($i = 0; $i < $this->numberOfRow; ++$i) {
            $retorno[$i][0] = $this->matriz[$i][$j];
        }

        return new Matriz($retorno);
    }

    public function setItem(int $i, int $j, int|float $value): self
    {

        if($i >= $this->numberOfRow || $j >= $this->numberOfCol) {
            throw new DomainException("Não existe essa entrada na Matriz");
        }
        $this->adicionarItem($i, $j, $value);
        return $this;
    }

    public function setCol(int $j, array |Matriz $valor): void
    {
        if (!isset($this->matriz[0][$j]) || \count($valor) > $this->numberOfRow) {
            throw new DomainException('Para adicionar uma coluna, a mesma deve ter o mesmo numero de linhas que a matriz');
        }
        $valor = is_array($valor) ? new Matriz($valor) : $valor;
        for ($i = 0; $i < $this->numberOfRow; ++$i) {
            $this->matriz[$i][$j] = sbNormalize((float)$valor[$i]);
        }
    }

    public function plus(Matriz $matriz): Matriz
    {
        if (!$this->matrizEMesmaOrdem($matriz)) {
            throw new DomainException('Para somar duas matrizes, é necessário que as mesmas possuem a mesma ordem');
        }
        $matrizSoma = [];

        for ($i = 0; $i < $this->numberOfRow; ++$i) {
            for ($j = 0; $j < $this->numberOfCol; ++$j) {
                $matrizSoma[$i][$j] = sbNormalize($this->item($i, $j) + $matriz->item($i, $j));
            }
        }

        return new self($matrizSoma);
    }

    public function multiply(Matriz $matriz): Matriz
    {
        if ($this->numberOfCol != $matriz->getM()) {
            throw new DomainException('Para multiplicar matrizes, a primeira matriz deve ter o numero de colunas igual ao da segunda.');
        }
        /** @var int[] */
        $matrizMultiplicacao = [];
        for ($i = 0; $i < $this->numberOfRow; ++$i) {
            for ($j = 0; $j < $matriz->getN(); ++$j) {
                $soma = 0;
                for ($k = 0; $k < $matriz->getM(); ++$k) {
                    $soma += $this->item($i, $k) * $matriz->item($k, $j);
                }
                $matrizMultiplicacao[$i][$j] = sbNormalize($soma);
            }
        }

        return new self($matrizMultiplicacao);
    }

    public function scalar(float|int $escala): Matriz
    {
        $matriz = [];
        for ($i = 0; $i < $this->numberOfRow; ++$i) {
            for ($j = 0; $j < $this->numberOfCol; ++$j) {
                $matriz[$i][$j] = $this->matriz[$i][$j] * $escala;
            }
        }

        return new self($matriz);
    }

    public function switchRow(int $i, int $iTroca): void
    {
        $linha = $this->getRow($i);
        $linhaTroca = $this->getRow($iTroca);

        $this->informarLinha($iTroca, $linha);
        $this->informarLinha($i, $linhaTroca);
    }

    public function transpose(): Matriz
    {
        $matriz = [];
        if (1 == $this->numberOfRow) {
            for ($j = 0; $j < $this->numberOfCol; ++$j) {
                $matriz[$j][0] = $this[$j];
            }

            return new self($matriz);
        }
        if (1 == $this->numberOfCol) {
            for ($j = 0; $j < $this->numberOfRow; ++$j) {
                $matriz[0][$j] = $this[$j];
            }

            return new self($matriz);
        }
        for ($i = 0; $i < $this->getM(); ++$i) {
            for ($j = 0; $j < $this->getN(); ++$j) {
                $matriz[$j][$i] = $this[$i][$j];
            }
        }

        return new self($matriz);
    }

    public function isIdentity(): bool
    {
        if (!$this->isSquare()) {
            return false;
        }
        $t = $this->getN();
        for ($i = 0; $i < $t; ++$i) {
            if (!sbIsZero($this->item($i, $i) - 1)) {
                return false;
            }
        }

        return true;
    }

    private function adicionarItem(int $i, int $j, int|float $valor): void
    {
        $this->matriz[$i][$j] = sbNormalize($valor);
    }

    private function informarLinha(int $i, array |Matriz $valor): void
    {
        $valor = is_array($valor) ? $valor : $valor->getMatriz();
        $this->matriz[$i] = array_map(fn(float|int $n) => sbNormalize($n), $valor);
    }


    /**
     * > It returns true if the number of rows and columns of the matrix passed as a parameter are the
     * same as the number of rows and columns of the matrix that called the function
     *
     * @param Matriz matriz The matrix to be compared.
     */
    private function matrizEMesmaOrdem(Matriz $matriz): bool
    {
        return $matriz->getM() === $this->numberOfRow || $matriz->getN() === $this->numberOfCol;
    }
}
