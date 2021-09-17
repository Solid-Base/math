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
        return $this->matriz;
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

    public function offsetExists($offset)
    {
        return isset($this->matriz[$offset]);
    }

    public function offsetGet($offset)
    {
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

    public function jsonSerialize()
    {
        return $this->matriz;
    }
}
