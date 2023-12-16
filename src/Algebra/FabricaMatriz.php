<?php

declare(strict_types=1);

namespace SolidBase\Math\Algebra;

class FabricaMatriz
{
    private function __construct() {}

    public static function Create(array $matriz): Matriz
    {
        return new Matriz($matriz);
    }

    public static function Identity(int $n): Matriz
    {
        $matriz = [];
        for ($i = 0; $i < $n; ++$i) {
            $linha = array_fill(0, $n, 0);
            $linha[$i] = 1;
            $matriz[$i] = $linha;
        }

        return new Matriz($matriz);
    }

    public static function Zero(int $n): Matriz
    {
        $matriz = [];
        for ($i = 0; $i < $n; ++$i) {
            $linha = array_fill(0, $n, 0);
            $matriz[$i] = $linha;
        }

        return new Matriz($matriz);
    }

    public static function Diagonal(array $diagonal): Matriz
    {
        $n = \count($diagonal);
        $matriz = [];
        for ($i = 0; $i < $n; ++$i) {
            for ($j = 0; $j < $n; ++$j) {
                if ($i === $j) {
                    $matriz[$i][$j] = $diagonal[$i];

                    continue;
                }

                $matriz[$i][$j] = 0;
            }
        }

        return new Matriz($matriz);
    }
}
