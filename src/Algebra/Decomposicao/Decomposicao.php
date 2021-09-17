<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra\Decomposicao;

use ArrayAccess;
use DomainException;
use SolidBase\Matematica\Algebra\Matriz;

abstract class Decomposicao implements ArrayAccess
{
    abstract public static function decompor(Matriz $M);

    abstract public function offsetExists($i): bool;

    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    public function offsetSet($offset, $value): void
    {
        throw new DomainException('Não é possível alterar valores isolados.');
    }

    public function offsetUnset($offset): void
    {
        throw new DomainException('Não é possivel apagar valores isolados.');
    }
}
