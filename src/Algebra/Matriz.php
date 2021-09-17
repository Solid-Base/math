<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra;

use DomainException;
use InvalidArgumentException;
use SolidBase\Matematica\Excecao\ExcecaoColunaNaoExiste;

class Matriz extends MatrizBase
{
    public function __construct(array $matriz)
    {
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

    public function adicionarColuna(array $coluna): void
    {
        if (\count($coluna) !== $this->NumeroLinha) {
            throw new DomainException('Para adicionar uma coluna, a mesma deve ter o mesmo numero de linhas que a matriz');
        }
        $j = $this->NumeroColuna;
        for ($i = 0; $i < \count($coluna); ++$i) {
            $this->matriz[$i][$j] = $coluna[$i];
        }
        ++$this->NumeroColuna;
    }

    public function adicionarLinha(array $linha): void
    {
        if (\count($linha) !== $this->NumeroColuna) {
            throw new DomainException('Para adicionar uma linha, a mesma deve ter o mesmo numero de colunas que a matriz');
        }
        $this->matriz[$this->NumeroLinha] = $linha;
        ++$this->NumeroLinha;
    }

    public function Item(int $i, $j): float
    {
        if (!isset($this->matriz[$i]) || !isset($this->matriz[$i][$j])) {
            throw new InvalidArgumentException('Não existe o item solicitado');
        }

        return $this->matriz[$i][$j];
    }

    public function obtenhaLinha($i): array
    {
        return $this->matriz[$i];
    }

    public function obtenhaColuna($j): array
    {
        if ($j >= $this->NumeroColuna) {
            throw new ExcecaoColunaNaoExiste();
        }
        $retorno = [];
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            $retorno[$i][0] = $this->matriz[$i][$j];
        }

        return $retorno;
    }

    public function informarColuna(int $j, array $valor): void
    {
        if (!isset($this->matriz[0][$j]) || \count($valor) > $this->NumeroLinha) {
            throw new DomainException('Para adicionar uma coluna, a mesma deve ter o mesmo numero de linhas que a matriz');
        }
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            $this->matriz[$i][$j] = $valor[$i][0];
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
                $matrizSoma[$i][$j] = $this->Item($i, $j) + $matriz->Item($i, $j);
            }
        }

        return new self($matrizSoma);
    }

    public function Multiplicar(self $matriz): self
    {
        if ($this->NumeroColuna !== $matriz->NumeroLinha) {
            throw new DomainException('Para multiplicar matrizes, a primeira matriz deve ter o numero de colunas igual ao da segunda.');
        }
        $matrizMultiplicacao = [];
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            for ($j = 0; $j < $matriz->NumeroColuna; ++$j) {
                $soma = 0;
                for ($k = 0; $k < $matriz->NumeroLinha; ++$k) {
                    $soma += $this->Item($i, $k) * $matriz->Item($k, $j);
                }
                $matrizMultiplicacao[$i][$j] = $soma;
            }
        }

        return new self($matrizMultiplicacao);
    }

    public function Escalar(float $escala): self
    {
        $matriz = [];
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            for ($j = 0; $j < $this->NumeroColuna; ++$j) {
                $matriz[$i][$j] = $this->matriz[$i][$j] * $escala;
            }
        }

        return new self($matriz);
    }

    public static function Identidade(float $n): self
    {
        $matriz = [];
        for ($i = 0; $i < $n; ++$i) {
            for ($j = 0; $j < $n; ++$j) {
                $matriz[$i][$j] = $i === $j ? 1 : 0;
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
        for ($i = 0; $i < $this->obtenhaM(); ++$i) {
            for ($j = 0; $j < $this->obtenhaN(); ++$j) {
                $matriz[$j][$i] = $this[$i][$j];
            }
        }

        return new self($matriz);
    }

    private function adicionarItem(int $i, int $j, float $valor): void
    {
        $this->matriz[$i][$j] = $valor;
    }

    private function informarLinha(int $i, array $valor): void
    {
        $this->matriz[$i] = $valor;
    }

    private function matrizEMesmaOrdem(self $matriz)
    {
        return $matriz->NumeroLinha === $this->NumeroLinha || $matriz->NumeroColuna === $this->NumeroColuna;
    }
}
