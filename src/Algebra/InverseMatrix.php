<?php

declare(strict_types=1);

namespace SolidBase\Math\Algebra;

use ArithmeticError;
use DomainException;
use SolidBase\Math\Algebra\Decomposition\LowerUpper;

class InverseMatrix
{
    public static function Inverse(Matriz $matriz): Matriz
    {
        if (!$matriz->isSquare()) {
            throw new DomainException('Para a matriz possuir inversa, a mesma deve ser quadrada.');
        }
        $ordem = $matriz->getN();
        $decomposicao = LowerUpper::Decompose($matriz);
        if (sbIsZero($decomposicao->Determinant())) {
            throw new ArithmeticError('Não é possível inverter a matriz');
        }
        $identidade = FabricaMatriz::Identity($ordem);
        $resultado = FabricaMatriz::Zero($ordem);
        for ($i = 0; $i < $ordem; ++$i) {
            $b = $identidade->getCol($i);
            $solucao = $decomposicao->SolveSystem($b);
            $resultado->setCol($i, $solucao);
        }

        return $resultado;
    }
}
