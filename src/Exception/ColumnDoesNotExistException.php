<?php

declare(strict_types=1);

namespace SolidBase\Math\Exception;

use OutOfBoundsException;
use Throwable;

class ColumnDoesNotExistException extends OutOfBoundsException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('A matriz não possui a coluna requerida', 101, $previous);
    }
}
