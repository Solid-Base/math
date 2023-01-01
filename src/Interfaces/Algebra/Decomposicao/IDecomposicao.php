<?php

namespace SolidBase\Matematica\Interfaces\Algebra\Decomposicao;

use SolidBase\Matematica\Interfaces\Algebra\IMatriz;

interface IDecomposicao
{
    public static function Decompor(IMatriz $M): self;
}
