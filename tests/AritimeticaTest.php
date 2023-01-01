<?php

$zero = ZERO_SOLIDBASE;
test("Ao passar um número negativo, deve retornar o mesmo positivo", function (int | float $number, $expected) {
    $modulo = modulo($number);
    expect($modulo)->toBe($expected);
})->with([
    "-5 espera 5"=>[-5,5],
    "-8 espera 8"=>[-8,8],
    "-6 espera 6"=>[-6,6],
    "-2 espera 2"=>[-2,2],
])->group("Aritimética");

test("Ao passar dois números, deve retornar o resto da divisão inteira", function (int | float $number1, int | float $number2, float $expected) {
    $mod = mod($number1, $number2);
    echo $mod;
    expect($mod)->toBe($expected);
})->with([
    "5%3 = 2" => [5,3,2.0],
    "6%3 = 0" => [6,3,0.0],
    "7%8 = 7" => [7,8,7.0],
    "7.5%4 = 3.5"=>[7.5,4,3.5]
])->group("Aritimética");

test("Ao passar dois números, retorna se são iguais ou não. Zero considerado: $zero", function (int | float $number1, int | float $number2, bool $estrito, bool $expected) {
    $igual = eIgual($number1, $number2, $estrito);
    expect($igual)->toBe($expected);
})->with([
    "2.0 e 2 em estrito = falso" => [2.0,2,true,false],
    "2.0 e 2 = verdadeiro" => [2.0,2,false,true],
    "1.99999999999999999 e 2 = verdadeiro" => [1.99999999999999999,2,false,true]
])->group("Aritimética");

test("Ao passar um numero, retorna se é zero ou não. Zero considerado: $zero", function (int|float $number, bool $expected) {
    $ezero = eZero($number);
    expect($ezero)->toBe($expected);
})->with([
    "0.000000000001 é zero"=> [0.000000000001,true],
    "0.00001 não é zero"=> [0.00001,false]
])->group("Aritimética");


test(
    "Ao passar três numeros, deve retornar se o segundo numero está entre o primeiro e o terceiro. Zero considerado: $zero",
    function (int | float $number1, int | float $number2, int | float $number3, bool $limites, bool $expected) {
        $entre = entre($number1, $number2, $number3, $limites);
        expect($entre)->toBe($expected);
    }
)->with([
    "2 está entre 1 e 3"=>[1,2,3,true,true],
    "2 está entre 2 e 3 se incluir os limites"=>[2,2,3,true,true],
    "2 não está entre 2 e 3 se não incluir os limites"=>[2,2,3,false,false],
    "2.999999999 está entre 2 e 3 se não incluir os limites"=>[2,2.999999999,3,false,true],
])->group("Aritimética");


test("Ao passar um numero, ele compara as casas e retorna se é inteiro ou não. Zero considerado: $zero", function (float|int $number, bool $expected) {
    $eInteiro = eInteiro($number);
    expect($eInteiro)->toBe($expected);
})->with([
    "1.99 não é inteiro"=>[1.99,false],
    "1.999999999 é inteiro"=>[1.999999999,true]
])->group("Aritimética");

test("Ao passar um numero, deve retornar ele normalizado em inteiro ou devolver o float. Zero considerado: $zero", function (float|int $number, float|int $expected) {
    $normalizar = normalizar($number);
    expect($normalizar)->toBe($expected);
})->with([
    "1.99 retorna 1.99" => [1.99,1.99],
    "1.999999999 retorna 2"=>[1.999999999,2.0]
])->group("Aritimética");
