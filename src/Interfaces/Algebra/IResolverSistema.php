<?php

namespace SolidBase\Matematica\Interfaces\Algebra;

interface IResolverSistema
{
    public function ResolverSistema(IMatriz $B): IMatriz;
}
