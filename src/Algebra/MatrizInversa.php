<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra;

use ArithmeticError;
use DomainException;
use SolidBase\Matematica\Algebra\Decomposicao\LU;

class MatrizInversa
{
    public const PRECISAO = 1E-10;

    public static function Inverter(Matriz $matriz): Matriz
    {
        if (!$matriz->eQuadrada()) {
            throw new DomainException('Para a matriz possuir inversa, a mesma deve ser quadrada.');
        }
        $ordem = $matriz->obtenhaN();
        $decomposicao = LU::Decompor($matriz);
        if ($decomposicao->Determinante() <= self::PRECISAO) {
            throw new ArithmeticError('Não é possível inverter a matriz');
        }
        $identidade = FabricaMatriz::Identidade($ordem);
        $resultado = FabricaMatriz::Nula($ordem);
        for ($i = 0; $i < $ordem; ++$i) {
            $b = new Matriz($identidade->obtenhaColuna($i));
            $solucao = $decomposicao->ResolverSistema($b);
            $resultado->informarColuna($i, $solucao->obtenhaColuna(0));
        }

        return $resultado;
    }
}
