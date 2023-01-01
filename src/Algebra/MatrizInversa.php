<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Algebra;

use ArithmeticError;
use DomainException;
use SolidBase\Matematica\Algebra\Decomposicao\LowerUpper;
use SolidBase\Matematica\Interfaces\Algebra\IMatriz;

class MatrizInversa
{
    public static function Inverter(IMatriz $matriz): IMatriz
    {
        if (!$matriz->eQuadrada()) {
            throw new DomainException('Para a matriz possuir inversa, a mesma deve ser quadrada.');
        }
        $ordem = $matriz->obtenhaN();
        $decomposicao = LowerUpper::Decompor($matriz);
        if (eZero($decomposicao->Determinante())) {
            throw new ArithmeticError('Não é possível inverter a matriz');
        }
        $identidade = FabricaMatriz::Identidade($ordem);
        $resultado = FabricaMatriz::Nula($ordem);
        for ($i = 0; $i < $ordem; ++$i) {
            $b = $identidade->obterColuna($i);
            $solucao = $decomposicao->ResolverSistema($b);
            $resultado->definirColuna($i, $solucao);
        }

        return $resultado;
    }
}
