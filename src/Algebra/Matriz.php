<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra;

use Countable;
use DomainException;
use InvalidArgumentException;
use SolidBase\Matematica\Excecao\ExcecaoColunaNaoExiste;

class Matriz extends MatrizBase implements Countable
{
    public function __construct(array|Matriz $matriz)
    {
        $matriz = is_a($matriz, Matriz::class) ? $matriz->obtenhaMatriz() : $matriz;
        $this->NumeroLinha = \count($matriz);
        $this->NumeroColuna = \is_array($matriz[0]) ? \count($matriz[0]) : 1;
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

    public function count(): int
    {
        return count($this->matriz);
    }

    public function offsetGet($offset): Matriz|float
    {
        if (1 == $this->NumeroLinha) {
            return $this->obtenhaColuna($offset)->obtenhaMatriz()[0];
        }
        if (1 == $this->NumeroColuna) {
            return $this->obtenhaLinha($offset)->obtenhaMatriz()[0];
        }

        return $this->obtenhaLinha($offset);
    }

    public function adicionarColuna(array $coluna): void
    {
        if (\count($coluna) != $this->NumeroLinha) {
            throw new DomainException('Para adicionar uma coluna, a mesma deve ter o mesmo numero de linhas que a matriz');
        }
        $j = $this->NumeroColuna;
        for ($i = 0; $i < \count($coluna); ++$i) {
            $this->adicionarItem($i, $j, $coluna[$i]);
        }
        ++$this->NumeroColuna;
    }

    public function adicionarLinha(array $linha): void
    {
        if (\count($linha) != $this->NumeroColuna) {
            throw new DomainException('Para adicionar uma linha, a mesma deve ter o mesmo numero de colunas que a matriz');
        }
        $this->matriz[$this->NumeroLinha] = array_map(fn (float|int $n) => normalizar($n), $linha);
        ++$this->NumeroLinha;
    }

    public function Item(int $i, int $j): float|int
    {
        if (!isset($this->matriz[$i]) || !isset($this->matriz[$i][$j])) {
            throw new InvalidArgumentException('Não existe o item solicitado');
        }

        return $this->matriz[$i][$j];
    }

    public function obtenhaLinha(int $i): Matriz
    {
        return new Matriz([array_map(fn (float|int $n) => $n, $this->matriz[$i])]);
    }

    public function obtenhaColuna(int $j): Matriz
    {
        if ($j >= $this->NumeroColuna) {
            throw new ExcecaoColunaNaoExiste();
        }
        $retorno = [];
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            $retorno[$i][0] = $this->matriz[$i][$j];
        }

        return new Matriz($retorno);
    }

    public function informarColuna(int $j, array|Matriz $valor): void
    {
        if (!isset($this->matriz[0][$j]) || \count($valor) > $this->NumeroLinha) {
            throw new DomainException('Para adicionar uma coluna, a mesma deve ter o mesmo numero de linhas que a matriz');
        }
        $valor = is_array($valor) ? new Matriz($valor) : $valor;
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            $this->matriz[$i][$j] = normalizar($valor[$i]);
        }
    }

    public function Somar(self $matriz): self
    {
        if (!$this->matrizEMesmaOrdem($matriz)) {
            throw new DomainException('Para somar duas matrizes, é necessário que as mesmas possuem a mesma ordem');
        }
        $matrizSoma = [];

        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            for ($j = 0; $j < $this->NumeroColuna; ++$j) {
                $matrizSoma[$i][$j] = normalizar($this->Item($i, $j) + $matriz->Item($i, $j));
            }
        }

        return new self($matrizSoma);
    }

    public function Multiplicar(self $matriz): self
    {
        if ($this->NumeroColuna != $matriz->NumeroLinha) {
            throw new DomainException('Para multiplicar matrizes, a primeira matriz deve ter o numero de colunas igual ao da segunda.');
        }
        $matrizMultiplicacao = [];
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            for ($j = 0; $j < $matriz->NumeroColuna; ++$j) {
                $soma = 0;
                for ($k = 0; $k < $matriz->NumeroLinha; ++$k) {
                    $soma += $this->Item($i, $k) * $matriz->Item($k, $j);
                }
                $matrizMultiplicacao[$i][$j] = normalizar($soma);
            }
        }

        return new self($matrizMultiplicacao);
    }

    public function Escalar(float|int $escala): self
    {
        $matriz = [];
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            for ($j = 0; $j < $this->NumeroColuna; ++$j) {
                $matriz[$i][$j] = $this->matriz[$i][$j] * $escala;
            }
        }

        return new self($matriz);
    }

    public static function Identidade(int $n): self
    {
        $matriz = [];
        for ($i = 0; $i < $n; ++$i) {
            for ($j = 0; $j < $n; ++$j) {
                $matriz[$i][$j] = $i == $j ? 1 : 0;
            }
        }

        return new self($matriz);
    }

    public function trocarLinha(int $i, int $iTroca): void
    {
        $linha = $this->obtenhaLinha($i);
        $linhaTroca = $this->obtenhaLinha($iTroca);

        $this->informarLinha($iTroca, $linha);
        $this->informarLinha($i, $linhaTroca);
    }

    public function Transposta(): self
    {
        $matriz = [];
        if (1 == $this->NumeroLinha) {
            for ($j = 0; $j < $this->NumeroColuna; ++$j) {
                $matriz[$j][0] = $this[$j];
            }

            return new self($matriz);
        }
        if (1 == $this->NumeroColuna) {
            for ($j = 0; $j < $this->NumeroLinha; ++$j) {
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
            if (!eZero($this->Item($i, $i) - 1)) {
                return false;
            }
        }

        return true;
    }

    private function adicionarItem(int $i, int $j, int|float $valor): void
    {
        $this->matriz[$i][$j] = normalizar($valor);
    }

    private function informarLinha(int $i, array|Matriz $valor): void
    {
        $valor = is_array($valor) ? $valor : $valor->obtenhaMatriz();
        $this->matriz[$i] = array_map(fn (float|int $n) => normalizar($n), $valor);
    }

    private function matrizEMesmaOrdem(self $matriz)
    {
        return $matriz->NumeroLinha === $this->NumeroLinha || $matriz->NumeroColuna === $this->NumeroColuna;
    }
}
