<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra;

use ArithmeticError;
use DomainException;
use SolidBase\Matematica\Algebra\Decomposicao\DecomposicaoLU;

class MatrizInversa
{
    public static function Inverter(Matriz $matriz): Matriz
    {
        if (!$matriz->eMatrizQuadrada()) {
            throw new DomainException('Para a matriz possuir inversa, a mesma deve ser quadrada.');
        }
        $ordem = $matriz->numeroColuna();
        $decomposicao = DecomposicaoLU::Decompor($matriz);
        if (eZero($decomposicao->Determinante())) {
            throw new ArithmeticError('Não é possível inverter a matriz');
        }
        $identidade = FabricaMatriz::Identidade($ordem);
        $resultado = FabricaMatriz::Nula($ordem);
        for ($i = 1; $i <= $ordem; ++$i) {
            $b = $identidade->obtenhaColuna($i);
            $solucao = $decomposicao->ResolverSistema($b);
            $resultado->definirColuna($i, $solucao);
        }

        return $resultado;
    }
}
