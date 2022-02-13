<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra;

use DomainException;

abstract class MatrizBase implements \ArrayAccess, \JsonSerializable
{
    protected array $matriz = [];

    protected int $NumeroLinha;
    protected int $NumeroColuna;

    public function obtenhaMatriz(): array
    {
        $retorno = [];
        if (1 == $this->NumeroLinha) {
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
        return $this->NumeroLinha;
    }

    public function obtenhaN(): int
    {
        return $this->NumeroColuna;
    }

    public function eQuadrada(): bool
    {
        return $this->NumeroColuna === $this->NumeroLinha;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->matriz[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        if (is_array($this->matriz[$offset])) {
            return $this->matriz[$offset];
        }

        return $this->matriz[$offset];
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
}
