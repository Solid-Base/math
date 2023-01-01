<?php

$zero = ZERO_SOLIDBASE;
test("Ao passar um angulo, deve retornar um angulo entre 0 e 2PI", function (int | float $number, float $expected) {
    $angulo = normatizarAngulo($number);
    expect($angulo)->toBe($expected);
})->with([
    "4PI espera 0"=>[4*M_PI,0.0],
    "-4PI espera 0"=>[-4*M_PI,0.0],
    "4.5PI espera PI/2"=>[4.5*M_PI,M_PI_2],
]);
