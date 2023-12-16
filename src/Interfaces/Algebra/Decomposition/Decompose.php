<?php

namespace SolidBase\Math\Interfaces\Algebra\Decomposition;

use SolidBase\Math\Algebra\Matriz;

interface Decompose
{
    public static function Decompose(Matriz $M): self;
}
