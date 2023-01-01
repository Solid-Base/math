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
    public function Item(int $i, int $j): float|int;
    public function eQuadrada(): bool;
    public function eIdentidade(): bool;
    public function adicionarColuna(array $coluna): void;
    public function adicionarLinha(array $linha): void;
    public function obtenhaLinha(int $i): IMatriz;
    public function obtenhaColuna(int $j): IMatriz;
    public function informarColuna(int $j, array |IMatriz $valor): void;
    public function trocarLinha(int $i, int $iTroca): void;
    public function Somar(IMatriz $matriz): IMatriz;
    public function Multiplicar(IMatriz $matriz): IMatriz;
    public function Escalar(float|int $escala): IMatriz;
    public function Transposta(): IMatriz;
}
