<?php

use SolidBase\Math\Algebra\FabricaMatriz;
use SolidBase\Math\Algebra\Matriz;
use SolidBase\Math\Algebra\InverseMatrix;

test("Dado uma matriz inversivel, deve retorar sua inversa", function (Matriz $matriz1) {
    $inversa = InverseMatrix::Inverse($matriz1);
    $multiplicao = $matriz1->multiply($inversa);
    $identidade = FabricaMatriz::Identity($multiplicao->getM());
    expect($multiplicao)->toEqual($identidade);
})->with([
    "matriz-1" => new Matriz([[1, 2, -3], [2, 1, 1], [-3, 2, 1]]),
])
    ->group("matriz");


test("Dado uma matriz não inversivel, deve lançar exception", function (Matriz $matriz1) {
    InverseMatrix::Inverse($matriz1);
})->with([
    "matriz-1" => [new Matriz([[1, 2, 3], [2, 4, 6], [2, 3, -1]])],
])->throws(ArithmeticError::class, "Não é possível inverter a matriz")
    ->group("matriz");

test("Dado uma matriz não quadrada, deve lançar exception", function (Matriz $matriz1) {
    InverseMatrix::Inverse($matriz1);
})->with([
    "matriz-1" => [new Matriz([[1, 2, 3], [2, 4, 6]])],
])->throws(DomainException::class, "Para a matriz possuir inversa, a mesma deve ser quadrada.")
    ->group("matriz");
