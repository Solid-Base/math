<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra;

use DomainException;
use InvalidArgumentException;
use SolidBase\Matematica\Aritimetica\Numero;
use SolidBase\Matematica\Excecao\ExcecaoColunaNaoExiste;

class Matriz extends MatrizBase
{
    private int $precisao;

    public function __construct(array $matriz)
    {
        $this->precisao = bcscale();
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
            $this->adicionarItem($i, $j, $coluna[$i]);
        }
        ++$this->NumeroColuna;
    }

    public function adicionarLinha(array $linha): void
    {
        if (\count($linha) !== $this->NumeroColuna) {
            throw new DomainException('Para adicionar uma linha, a mesma deve ter o mesmo numero de colunas que a matriz');
        }
        $this->matriz[$this->NumeroLinha] = array_map(fn (mixed $n) => numero($n), $linha);
        ++$this->NumeroLinha;
    }

    public function Item(int $i, $j, $real = true): float|Numero
    {
        if (!isset($this->matriz[$i]) || !isset($this->matriz[$i][$j])) {
            throw new InvalidArgumentException('Não existe o item solicitado');
        }
        if ($real) {
            return $this->matriz[$i][$j]->valor();
        }

        return $this->matriz[$i][$j];
    }

    public function obtenhaLinha($i): array
    {
        return array_map(fn (Numero $n) => $n->valor(), $this->matriz[$i]);
    }

    public function obtenhaColuna($j): array
    {
        if ($j >= $this->NumeroColuna) {
            throw new ExcecaoColunaNaoExiste();
        }
        $retorno = [];
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            $retorno[$i][0] = $this->matriz[$i][$j]->valor();
        }

        return $retorno;
    }

    public function informarColuna(int $j, array $valor): void
    {
        if (!isset($this->matriz[0][$j]) || \count($valor) > $this->NumeroLinha) {
            throw new DomainException('Para adicionar uma coluna, a mesma deve ter o mesmo numero de linhas que a matriz');
        }
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            $this->matriz[$i][$j] = numero($valor[$i][0]);
        }
    }

    public function Somar(self $matriz): self
    {
        if (!$this->matrizEMesmaOrdem($matriz)) {
            throw new DomainException('Para somar duas matrizes, é necessário que as mesmas possuem a mesma ordem');
        }
        $matrizSoma = [];
        $precisao = max($this->precisao, $matriz->precisao);
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            for ($j = 0; $j < $this->NumeroColuna; ++$j) {
                $matrizSoma[$i][$j] = somar($this->Item($i, $j, false), ($matriz->Item($i, $j)));
            }
        }

        $retorno = new self($matrizSoma);
        $retorno->precisao = $precisao;

        return $retorno;
    }

    public function Multiplicar(self $matriz): self
    {
        if ($this->NumeroColuna !== $matriz->NumeroLinha) {
            throw new DomainException('Para multiplicar matrizes, a primeira matriz deve ter o numero de colunas igual ao da segunda.');
        }
        $matrizMultiplicacao = [];
        $precisaoMultiplicacao = $this->precisao * $matriz->precisao;
        $precisaoRetorno = max($matriz->precisao, $this->precisao);
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            for ($j = 0; $j < $matriz->NumeroColuna; ++$j) {
                $soma = numero(0, $precisaoMultiplicacao);
                for ($k = 0; $k < $matriz->NumeroLinha; ++$k) {
                    $soma->somar(multiplicar($this->Item($i, $k), $matriz->Item($k, $j)));
                }
                $matrizMultiplicacao[$i][$j] = eZero($soma) ? numero(0, $precisaoRetorno) : arredondar($soma, $precisaoRetorno);
            }
        }

        $retorno = new self($matrizMultiplicacao);
        $retorno->precisao = $precisaoRetorno;

        return $retorno;
    }

    public function Escalar(float|Numero $escala): self
    {
        $matriz = [];
        for ($i = 0; $i < $this->NumeroLinha; ++$i) {
            for ($j = 0; $j < $this->NumeroColuna; ++$j) {
                $matriz[$i][$j] = multiplicar($this->matriz[$i][$j], $escala);
            }
        }

        $retorno = new self($matriz);
        $retorno->precisao = $this->precisao;

        return $retorno;
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

        $retorno = new self($matriz);
        $retorno->precisao = $this->precisao;

        return $retorno;
    }

    /**
     * Informar o valor precisao.
     */
    public function informarPrecisao(int $precisao): self
    {
        $this->precisao = $precisao;

        return $this;
    }

    public function obtenhaPrecisao(): int
    {
        return $this->precisao;
    }

    private function adicionarItem(int $i, int $j, int|string|Numero|float $valor): void
    {
        $numero = numero($valor, $this->precisao);
        $this->precisao = max($this->precisao, $numero->precisao);
        $numero = eInteiro($numero) ? numero($numero->inteiro(),$this->precisao) : $numero;
        $this->matriz[$i][$j] = $numero;
    }

    private function informarLinha(int $i, array $valor): void
    {
        $this->matriz[$i] = array_map(fn (float|Numero|int $n) => numero($n), $valor);
    }

    private function matrizEMesmaOrdem(self $matriz)
    {
        return $matriz->NumeroLinha === $this->NumeroLinha || $matriz->NumeroColuna === $this->NumeroColuna;
    }
}
