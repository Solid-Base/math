<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra;

use DomainException;
use InvalidArgumentException;
use SolidBase\Matematica\Excecao\ExcecaoColunaNaoExiste;
use SolidBase\Matematica\Interfaces\Algebra\IMatriz;

class Matriz implements IMatriz
{
    public function __construct(array |Matriz $matriz)
    {
        $matriz = is_a($matriz, Matriz::class) ? $matriz->obtenhaMatriz() : $matriz;
        $this->numeroLinha = \count($matriz);
        $this->numeroColuna = \is_array($matriz[0]) ? \count($matriz[0]) : 1;
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

    protected int $numeroLinha;
    protected int $numeroColuna;

    public function obtenhaMatriz(): array
    {
        $retorno = [];
        if (1 == $this->numeroLinha) {
            return array_map(fn (float|int $n) => normalizar($n), $this->matriz[0]);
        }
        foreach ($this->matriz as $linha => $valores) {
            foreach ($valores as $coluna => $valor) {
                $retorno[$linha][$coluna] = normalizar($valor);
            }
        }

        return $retorno;
    }

    public function obtenhaM(): int
    {
        return $this->numeroLinha;
    }

    public function obtenhaN(): int
    {
        return $this->numeroColuna;
    }

    public function eQuadrada(): bool
    {
        return $this->numeroColuna === $this->numeroLinha;
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

    public function offsetGet($offset): IMatriz|float
    {
        if (1 == $this->numeroLinha) {
            return $this->obterColuna($offset)->obtenhaMatriz()[0];
        }
        if (1 == $this->numeroColuna) {
            return $this->obterLinha($offset)->obtenhaMatriz()[0];
        }
        return $this->obterLinha($offset);
    }

    public function adicionarColuna(array $coluna): void
    {
        if (\count($coluna) != $this->numeroLinha) {
            throw new DomainException('Para adicionar uma coluna, a mesma deve ter o mesmo numero de linhas que a matriz');
        }
        $j = $this->numeroColuna;
        for ($i = 0; $i < \count($coluna); ++$i) {
            $this->adicionarItem($i, $j, $coluna[$i]);
        }
        ++$this->numeroColuna;
    }

    public function adicionarLinha(array $linha): void
    {
        if (\count($linha) != $this->numeroColuna) {
            throw new DomainException('Para adicionar uma linha, a mesma deve ter o mesmo numero de colunas que a matriz');
        }
        $this->matriz[$this->numeroLinha] = array_map(fn (float|int $n) => normalizar($n), $linha);
        ++$this->numeroLinha;
    }

    public function item(int $i, int $j): float|int
    {
        if (!isset($this->matriz[$i]) || !isset($this->matriz[$i][$j])) {
            throw new InvalidArgumentException('Não existe o item solicitado');
        }

        return $this->matriz[$i][$j];
    }

    public function obterLinha(int $i): IMatriz
    {
        $matriz = [0 => $this->matriz[$i]];

        return new Matriz($matriz);
    }

    public function obterColuna(int $j): IMatriz
    {
        if ($j >= $this->numeroColuna) {
            throw new ExcecaoColunaNaoExiste();
        }
        $retorno = [];
        for ($i = 0; $i < $this->numeroLinha; ++$i) {
            $retorno[$i][0] = $this->matriz[$i][$j];
        }

        return new Matriz($retorno);
    }

    public function definirColuna(int $j, array |IMatriz $valor): void
    {
        if (!isset($this->matriz[0][$j]) || \count($valor) > $this->numeroLinha) {
            throw new DomainException('Para adicionar uma coluna, a mesma deve ter o mesmo numero de linhas que a matriz');
        }
        $valor = is_array($valor) ? new Matriz($valor) : $valor;
        for ($i = 0; $i < $this->numeroLinha; ++$i) {
            $this->matriz[$i][$j] = normalizar((float)$valor[$i]);
        }
    }

    public function somar(IMatriz $matriz): IMatriz
    {
        if (!$this->matrizEMesmaOrdem($matriz)) {
            throw new DomainException('Para somar duas matrizes, é necessário que as mesmas possuem a mesma ordem');
        }
        $matrizSoma = [];

        for ($i = 0; $i < $this->numeroLinha; ++$i) {
            for ($j = 0; $j < $this->numeroColuna; ++$j) {
                $matrizSoma[$i][$j] = normalizar($this->item($i, $j) + $matriz->item($i, $j));
            }
        }

        return new self($matrizSoma);
    }

    public function multiplicar(IMatriz $matriz): IMatriz
    {
        if ($this->numeroColuna != $matriz->obtenhaM()) {
            throw new DomainException('Para multiplicar matrizes, a primeira matriz deve ter o numero de colunas igual ao da segunda.');
        }
        /** @var int[] */
        $matrizMultiplicacao = [];
        for ($i = 0; $i < $this->numeroLinha; ++$i) {
            for ($j = 0; $j < $matriz->obtenhaN(); ++$j) {
                $soma = 0;
                for ($k = 0; $k < $matriz->obtenhaM(); ++$k) {
                    $soma += $this->item($i, $k) * $matriz->item($k, $j);
                }
                $matrizMultiplicacao[$i][$j] = normalizar($soma);
            }
        }

        return new self($matrizMultiplicacao);
    }

    public function escalar(float|int $escala): IMatriz
    {
        $matriz = [];
        for ($i = 0; $i < $this->numeroLinha; ++$i) {
            for ($j = 0; $j < $this->numeroColuna; ++$j) {
                $matriz[$i][$j] = $this->matriz[$i][$j] * $escala;
            }
        }

        return new self($matriz);
    }

    public function trocarLinha(int $i, int $iTroca): void
    {
        $linha = $this->obterLinha($i);
        $linhaTroca = $this->obterLinha($iTroca);

        $this->informarLinha($iTroca, $linha);
        $this->informarLinha($i, $linhaTroca);
    }

    public function transposta(): IMatriz
    {
        $matriz = [];
        if (1 == $this->numeroLinha) {
            for ($j = 0; $j < $this->numeroColuna; ++$j) {
                $matriz[$j][0] = $this[$j];
            }

            return new self($matriz);
        }
        if (1 == $this->numeroColuna) {
            for ($j = 0; $j < $this->numeroLinha; ++$j) {
                $matriz[0][$j] = $this[$j];
            }

            return new self($matriz);
        }
        for ($i = 0; $i < $this->obtenhaM(); ++$i) {
            for ($j = 0; $j < $this->obtenhaN(); ++$j) {
                $matriz[$j][$i] = $this[$i][$j];
            }
        }

        return new self($matriz);
    }

    public function eIdentidade(): bool
    {
        if (!$this->eQuadrada()) {
            return false;
        }
        $t = $this->obtenhaN();
        for ($i = 0; $i < $t; ++$i) {
            if (!eZero($this->item($i, $i) - 1)) {
                return false;
            }
        }

        return true;
    }

    private function adicionarItem(int $i, int $j, int|float $valor): void
    {
        $this->matriz[$i][$j] = normalizar($valor);
    }

    private function informarLinha(int $i, array |Matriz $valor): void
    {
        $valor = is_array($valor) ? $valor : $valor->obtenhaMatriz();
        $this->matriz[$i] = array_map(fn (float|int $n) => normalizar($n), $valor);
    }


    /**
     * > It returns true if the number of rows and columns of the matrix passed as a parameter are the
     * same as the number of rows and columns of the matrix that called the function
     *
     * @param IMatriz matriz The matrix to be compared.
     */
    private function matrizEMesmaOrdem(IMatriz $matriz): bool
    {
        return $matriz->obtenhaM() === $this->numeroLinha || $matriz->obtenhaN() === $this->numeroColuna;
    }
}
