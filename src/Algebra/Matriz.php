<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra;

use ArrayAccess;
use Countable;
use DomainException;
use SolidBase\Matematica\Excecao\ExcecaoColunaNaoExiste;
use SolidBase\Matematica\Excecao\ExcecaoElementoNaoExiste;

final class Matriz implements ArrayAccess, Countable
{
    private array $matriz;
    private int $numeroLinha;
    private int $numeroColuna;

    public function __construct(array|Matriz $matriz)
    {
        if ($matriz instanceof Matriz) {
            $this->matriz = $matriz->matriz;
            $this->numeroColuna = $matriz->numeroColuna();
            $this->numeroLinha = $matriz->numeroLinha();

            return;
        }
        $this->numeroLinha = count($matriz);
        $this->numeroColuna = count($matriz[0]);
        for ($i = 0; $i < $this->numeroLinha; ++$i) {
            for ($j = 0; $j < $this->numeroColuna; ++$j) {
                $this->matriz[$i][$j] = $matriz[$i][$j] ?? 0;
            }
        }
    }

    /**
     * @param string $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        if ($this->eSeparadorUnitario($offset)) {
            $j = intval($offset);

            return (1 == $this->numeroColuna || 1 == $this->numeroLinha) && ($j <= $this->numeroColuna || $j <= $this->numeroLinha);
        }
        [$i,$j] = $this->explodeOffset($offset);

        return entre(1, $i + 1, $this->numeroLinha) && entre(1, $j + 1, $this->numeroColuna);
    }

    /**
     * @param string $offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (!$this->offsetExists($offset)) {
            throw new ExcecaoElementoNaoExiste();
        }
        if (!$this->eSeparadorUnitario($offset)) {
            [$i,$j] = $this->explodeOffset($offset);

            return $this->obtenhaElemento($i + 1, $j + 1);
        }
        $j = intval($offset);
        if (1 == $this->numeroColuna) {
            return $this->obtenhaElemento($j, 1);
        }
        if (1 == $this->numeroLinha) {
            return $this->obtenhaElemento(1, $j);
        }
        $this->obtenhaLinha($j);
    }

    public function obtenhaElemento(int $i, int $j): float
    {
        if ($this->offsetExists("{$i},{$j}")) {
            return normalizar($this->matriz[$i - 1][$j - 1] ?? 0);
        }

        throw new DomainException('Não existe o elemento nessa posição');
    }

    public function definirElemento(int $i, int $j, float $valor): static
    {
        if (!(entre(1, $i, $this->numeroLinha) || entre(1, $j, $this->numeroColuna))) {
            throw new DomainException('As posições informadas não são compativeis com a matriz');
        }
        $this->matriz[$i - 1][$j - 1] = $valor;

        return $this;
    }

    public function definirLinha(int $i, array|Matriz $matriz): static
    {
        if (!entre(1, $i, $this->numeroLinha) || ($matriz instanceof Matriz && !$matriz->eMatrizLinha())) {
            throw new DomainException('As posição de linha informada não é compativeis com a matriz');
        }
        $valores = $matriz instanceof Matriz ? $matriz->matriz[0] : $matriz;
        foreach ($valores as $j => $valor) {
            $this->definirElemento($i, $j + 1, $valor);
        }

        return $this;
    }

    public function definirColuna(int $j, array|Matriz $matriz): static
    {
        if (!entre(1, $j, $this->numeroColuna) || ($matriz instanceof Matriz && !$matriz->eMatrizColuna())) {
            throw new DomainException('As posição de coluna informada não é compativeis com a matriz');
        }

        $valores = $matriz instanceof Matriz ? $matriz->matriz : $matriz;
        foreach ($valores as $i => $valor) {
            $this->definirElemento($i + 1, $j, $valor[0]);
        }

        return $this;
    }

    /**
     * @param string                 $offset
     * @param array|float|int|Matriz $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_float($value) || is_int($value)) {
            if ($this->eSeparadorUnitario($offset)) {
                throw new DomainException('As posições informadas não são compativeis com a matriz');
            }
            [$i,$j] = $this->explodeOffset($offset);
            $this->definirElemento($i + 1, $j + 1, $value);

            return;
        }

        if ($this->eSeparadorUnitario($offset) && ($value instanceof Matriz || is_array($value))) {
            $value = $value instanceof Matriz ? $value->matriz : $value;
            if (1 == count($value)) {
                $this->definirLinha(intval($offset), $value[0]);

                return;
            }
            $this->definirColuna(intval($offset), $value);
        }
    }

    /**
     * @param string $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        if ($this->eSeparadorUnitario($offset)) {
            $valores = array_fill(1, $this->numeroColuna, 0);
            $this->definirLinha(intval($offset), $valores);

            return;
        }
        [$i,$j] = $this->explodeOffset($offset);

        $this->definirElemento($i, $j, 0);
    }

    public function count(): int
    {
        return $this->numeroColuna * $this->numeroLinha;
    }

    public function obtenhaColuna(int $j): Matriz
    {
        if (!entre(1, $j, $this->numeroColuna)) {
            throw new ExcecaoColunaNaoExiste();
        }
        $matrizRetorno = [];
        for ($i = 0; $i < $this->numeroLinha; ++$i) {
            $matrizRetorno[$i] = [$this->obtenhaElemento($i + 1, $j)];
        }

        return new Matriz($matrizRetorno);
    }

    public function obtenhaLinha(int $i): Matriz
    {
        if (!entre(1, $i, $this->numeroLinha)) {
            throw new ExcecaoColunaNaoExiste();
        }
        $retorno = $this->matriz[$i - 1];

        return new Matriz([$retorno]);
    }

    public function numeroLinha(): int
    {
        return $this->numeroLinha;
    }

    public function numeroColuna(): int
    {
        return $this->numeroColuna;
    }

    public function eMatrizLinha(): bool
    {
        return 1 == $this->numeroLinha;
    }

    public function eMatrizColuna(): bool
    {
        return 1 == $this->numeroColuna;
    }

    public function eMatrizQuadrada(): bool
    {
        return $this->numeroColuna() == $this->numeroLinha();
    }

    public function eMatrizIdentidade(): bool
    {
        if (!$this->eMatrizQuadrada()) {
            return false;
        }

        for ($i = 1; $i <= $this->numeroLinha(); ++$i) {
            for ($j = 1; $j <= $this->numeroColuna(); ++$j) {
                if (($i == $j && !eIgual($this->obtenhaElemento($i, $i), 1))) {
                    return false;
                }
                if ($i != $j && !eZero($this->obtenhaElemento($i, $j))) {
                    return false;
                }
            }
        }

        return true;
    }

    // Operações de matrizes

    public function somar(self $matriz): self
    {
        if (!$this->matrizEMesmaOrdem($matriz)) {
            throw new DomainException('Para somar duas matrizes, é necessário que as mesmas possuem a mesma ordem');
        }
        $matrizSoma = [];

        for ($i = 1; $i <= $this->numeroLinha; ++$i) {
            for ($j = 1; $j <= $this->numeroColuna; ++$j) {
                $matrizSoma[$i - 1][$j - 1] = normalizar($this->obtenhaElemento($i, $j) + $matriz->obtenhaElemento($i, $j));
            }
        }

        return new self($matrizSoma);
    }

    public function multiplicar(self $matriz): self
    {
        if ($this->numeroColuna() != $matriz->numeroLinha()) {
            throw new DomainException('Para multiplicar matrizes, a primeira matriz deve ter o numero de colunas igual ao da segunda.');
        }
        $matrizMultiplicacao = [];
        for ($i = 1; $i <= $this->numeroLinha(); ++$i) {
            for ($j = 1; $j <= $matriz->numeroColuna(); ++$j) {
                $soma = 0;
                for ($k = 1; $k <= $matriz->numeroLinha(); ++$k) {
                    $soma += $this->obtenhaElemento($i, $k) * $matriz->obtenhaElemento($k, $j);
                }
                $matrizMultiplicacao[$i - 1][$j - 1] = normalizar($soma);
            }
        }

        return new self($matrizMultiplicacao);
    }

    public function escalar(float|int $escala): self
    {
        $matriz = [];
        for ($i = 1; $i <= $this->numeroLinha(); ++$i) {
            for ($j = 1; $j <= $this->numeroColuna(); ++$j) {
                $matriz[$i - 1][$j - 1] = $this->obtenhaElemento($i, $j) * $escala;
            }
        }

        return new self($matriz);
    }

    public function transposta(): self
    {
        $matriz = [];
        if (1 == $this->numeroLinha()) {
            for ($j = 1; $j <= $this->numeroColuna(); ++$j) {
                $matriz[$j - 1] = [$this->obtenhaElemento(1, $j)];
            }

            return new self($matriz);
        }
        if (1 == $this->numeroColuna()) {
            for ($j = 1; $j <= $this->numeroLinha(); ++$j) {
                $matriz[0][$j - 1] = $this->obtenhaElemento($j, 1);
            }

            return new self($matriz);
        }
        for ($i = 1; $i <= $this->numeroLinha(); ++$i) {
            for ($j = 1; $j <= $this->numeroColuna(); ++$j) {
                $matriz[$j - 1][$i - 1] = $this->obtenhaElemento($i, $j);
            }
        }

        return new self($matriz);
    }

    public function trocarLinha(int $i, int $iTroca): void
    {
        $linha = $this->obtenhaLinha($i);
        $linhaTroca = $this->obtenhaLinha($iTroca);

        $this->definirLinha($iTroca, $linha);
        $this->definirLinha($i, $linhaTroca);
    }

    public function trocarColuna(int $j, int $jTroca): void
    {
        $coluna = $this->obtenhaColuna($j);
        $colunaTroca = $this->obtenhaColuna($jTroca);

        $this->definirColuna($jTroca, $coluna);
        $this->definirColuna($j, $colunaTroca);
    }

    public function adicionarColuna(array|Matriz $coluna): static
    {
        ++$this->numeroColuna;
        $this->definirColuna($this->numeroColuna, $coluna);

        return $this;
    }

    public function adicionarLinha(array|Matriz $linha): static
    {
        ++$this->numeroLinha;
        $this->definirLinha($this->numeroLinha, $linha);

        return $this;
    }

    private function numeroSeparadorOffset(string $offset): int
    {
        return mb_substr_count($offset, ',');
    }

    private function eSeparadorUnitario(string $offset): bool
    {
        return 0 == $this->numeroSeparadorOffset($offset);
    }

    private function explodeOffset(string $offset): array
    {
        [$i,$j] = explode(',', $offset);

        return [intval($i) - 1, intval($j) - 1];
    }

    private function matrizEMesmaOrdem(self $matriz): bool
    {
        return $matriz->numeroColuna() == $this->numeroColuna() && $matriz->numeroLinha() == $this->numeroLinha();
    }
}
