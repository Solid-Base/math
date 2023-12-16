<?php

declare(strict_types=1);

namespace SolidBase\Math\Exception;

use OutOfBoundsException;
use Throwable;

class ElementDoesNotExistException extends OutOfBoundsException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('A matriz não possui o elemento na posição informada', 101, $previous);
    }
}
