<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Geometria;

use DomainException;

class Poligono
{
    private float $anguloInterno;
    private float $anguloExterno;
    private float $lado;
    private float $raio;

    public function __construct(private int $numeroLados)
    {
        $this->calcularAnguloInterno();
        $this->calcularAnguloExterno();
    }

    public function numeroLados(): int
    {
        return $this->numeroLados;
    }

    public function informarLado(float $lado): void
    {
        $this->lado = $lado;
        $this->calcularRaioLado($lado);
    }

    public function informarRaio(float $raio): void
    {
        $this->raio = $raio;
        $this->calcularLadoRaio($raio);
    }

    public function informarApotema(float $apotema): void
    {
        $raio = $apotema / sin($this->anguloInterno / 2);
        $this->informarRaio($raio);
    }

    public function apotema(): float
    {
        return sqrt($this->raio ** 2 - ($this->lado ** 2) / 4);
    }

    public function altura(): float
    {
        if (0 === $this->numeroLados % 2) {
            return 2 * $this->apotema();
        }

        return $this->apotema() + $this->raio;
    }

    public function raio(): float
    {
        return $this->raio;
    }

    public function lado(): float
    {
        return $this->lado;
    }

    public function maiorDiagonal(): float
    {
        if ($this->numeroLados <= 3) {
            throw new DomainException('Não é possível calcular para triangulos');
        }

        if (0 === $this->numeroLados % 2) {
            return 2 * $this->raio;
        }

        return $this->altura() / (sin(($this->anguloInterno * ($this->numeroLados - 1)) / (2 * ($this->numeroLados - 2))));
    }

    public function anguloInterno(): float
    {
        return $this->anguloInterno;
    }

    public function anguloExterno(): float
    {
        return $this->anguloExterno;
    }

    public function area(): float
    {
        return $this->perimetro() * $this->apotema() / 2;
    }

    public function perimetro(): float
    {
        return $this->numeroLados * $this->lado;
    }

    private function calcularRaioLado(float $lado): void
    {
        $this->raio = $this->lado / (2 * cos($this->anguloInterno / 2));
    }

    private function calcularLadoRaio(float $raio): void
    {
        $this->lado = $raio * 2 * cos($this->anguloInterno / 2);
    }

    private function calcularAnguloInterno(): void
    {
        $this->anguloInterno = M_PI - 2 * M_PI / $this->numeroLados;
    }

    private function calcularAnguloExterno(): void
    {
        $this->anguloExterno = 2 * M_PI / $this->numeroLados;
    }
}
