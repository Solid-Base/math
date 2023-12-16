<?php

namespace SolidBase\Math\Interfaces\Algebra;

use SolidBase\Math\Algebra\Matriz;

interface SolveSystem
{
    public function SolveSystem(Matriz $B): Matriz;
}
