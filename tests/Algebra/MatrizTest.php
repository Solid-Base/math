<?php

use SolidBase\Math\Algebra\Decomposition\LowerUpper;
use SolidBase\Math\Algebra\Matriz;

test("Deve somar duas matrizes", function (Matriz $matriz1, Matriz $matriz2, Matriz $espect) {
    $soma = $matriz1->plus($matriz2);
    expect($soma)->toEqual($espect);
})->with([
    "matriz-1" => [new Matriz([[2,1],[8,4],[5,10]]),new Matriz([[1,2],[3,4],[5,6]]),new Matriz([[3,3],[11,8],[10,16]])],
    "matriz-2" => [new Matriz([[1,2],[5,4]]),new Matriz([[1,2],[5,4]]), new Matriz([[2,4],[10,8]])],
    ])
->group("matriz");


test("Deve multiplicar duas matrizes", function (Matriz $matriz1, Matriz $matriz2, Matriz $espect) {
    $soma = $matriz1->multiply($matriz2);
    expect($soma)->toEqual($espect);
})->with([
    "matriz-1" => [new Matriz([[3,2],[5,-1]]),new Matriz([[6,4,-2],[0,7,1]]),new Matriz([[18,26,-4],[30,13,-11]])],
    "matriz-2" => [new Matriz([[3,5],[1,2]]),new Matriz([[2,-5],[-1,3]]),new Matriz([[1,0],[0,1]])],
    ])
->group("matriz");


test("Deve multiplicar uma matriz por um escalar e devolver uma matriz nova", function (Matriz $matriz1, float|int $escalar, Matriz $espect) {
    $soma = $matriz1->scalar($escalar);
    expect($soma)->toEqual($espect);
})->with([
    "matriz-1" => [new Matriz([[3,2],[5,-1]]),2,new Matriz([[6,4],[10,-2]])],
    "matriz-2" => [new Matriz([[3,5],[1,2]]),5.0,new Matriz([[15,25],[5,10]])],
    ])
->group("matriz");


test("Dado uma matriz, deve retornar a sua determinante", function (Matriz $matriz1, float $expect) {
    $lu = LowerUpper::Decompose($matriz1);
    $determinante = $lu->Determinant();
    expect($determinante)->toEqual($expect);
})->with([
    "matriz-1" => [new Matriz([[1,2,3],[2,4,6],[2,3,-1]]),0],
    "matriz-2" => [new Matriz([[1,9,5],[3,7,8],[10,4,2]]),358],
    "matriz-3" => [new Matriz([[1,-3,2],[4,2,0],[-2,1,3]]),58],
    ])
->group("matriz");

test("Dado um sistema linear, deve retornar uma matriz com a sua solução", function (Matriz $matriz1, Matriz $variaveis, Matriz $expect) {
    $lu = LowerUpper::Decompose($matriz1);
    $solucao = $lu->SolveSystem($variaveis);
    expect($solucao)->toEqual($expect);
})->with([
    "matriz-1" => [new Matriz([[1,1],[2,1]]),new Matriz([[19],[31]]),new Matriz([[12],[7]])],
    "matriz-2" => [new Matriz([[2,2],[1,4]]),new Matriz([[9],[18]]),new Matriz([[0],[4.5]])],
    "matriz-3" => [new Matriz([[1,2,-3],[2,1,1],[-3,2,1]]),new Matriz([[10],[3],[-6]]),new Matriz([[2],[1],[-2]])],
]);

test("Dado um sistema linear sem solução, deve retornar uma excessao", function (Matriz $matriz1, Matriz $variaveis) {
    $lu = LowerUpper::Decompose($matriz1);
    $lu->SolveSystem($variaveis);
})->with([
    "matriz-3" => [new Matriz([[1,2,3],[2,4,6],[2,3,-1]]),new Matriz([[10],[3],[-6]])],
])->throws(DomainException::class, "O sistema não possui solução!");
