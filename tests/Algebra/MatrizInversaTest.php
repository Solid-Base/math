<?php

use SolidBase\Matematica\Algebra\FabricaMatriz;
use SolidBase\Matematica\Algebra\Matriz;
use SolidBase\Matematica\Algebra\MatrizInversa;

test("Dado uma matriz inversivel, deve retorar sua inversa", function (Matriz $matriz1) {
    $inversa = MatrizInversa::Inverter($matriz1);
    $multiplicao = $matriz1->multiplicar($inversa);
    $identidade = FabricaMatriz::Identidade($multiplicao->obtenhaM());
    expect($multiplicao)->toEqual($identidade);
})->with([
    "matriz-1" => new Matriz([[1, 2, -3], [2, 1, 1], [-3, 2, 1]]),
])
    ->group("matriz");


test("Dado uma matriz não inversivel, deve lançar exception", function (Matriz $matriz1) {
    MatrizInversa::Inverter($matriz1);
})->with([
    "matriz-1" => [new Matriz([[1, 2, 3], [2, 4, 6], [2, 3, -1]])],
])->throws(ArithmeticError::class, "Não é possível inverter a matriz")
    ->group("matriz");

test("Dado uma matriz não quadrada, deve lançar exception", function (Matriz $matriz1) {
    MatrizInversa::Inverter($matriz1);
})->with([
    "matriz-1" => [new Matriz([[1, 2, 3], [2, 4, 6]])],
])->throws(DomainException::class, "Para a matriz possuir inversa, a mesma deve ser quadrada.")
    ->group("matriz");
