<?php

namespace SolidBase\Matematica\Interfaces\Algebra;

use ArrayAccess;
use Countable;
use JsonSerializable;

interface IMatriz extends Countable, ArrayAccess, JsonSerializable
{
    public function obtenhaN(): int;
    public function obtenhaM(): int;
    public function obtenhaMatriz(): array;
    public function item(int $i, int $j): float|int;
    public function eQuadrada(): bool;
    public function eIdentidade(): bool;
    public function adicionarColuna(array $coluna): void;
    public function adicionarLinha(array $linha): void;
    public function obterLinha(int $i): IMatriz;
    public function obterColuna(int $j): IMatriz;
    public function definirColuna(int $j, array |IMatriz $valor): void;
    public function trocarLinha(int $i, int $iTroca): void;
    public function somar(IMatriz $matriz): IMatriz;
    public function multiplicar(IMatriz $matriz): IMatriz;
    public function escalar(float|int $escala): IMatriz;
    public function transposta(): IMatriz;
}
