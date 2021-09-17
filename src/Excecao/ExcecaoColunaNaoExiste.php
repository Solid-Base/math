<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Excecao;

use OutOfBoundsException;
use Throwable;

class ExcecaoColunaNaoExiste extends OutOfBoundsException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('A matriz não possui a coluna requerida', 101, $previous);
    }
}
