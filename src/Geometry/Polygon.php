<?php

declare(strict_types=1);

namespace SolidBase\Math\Geometry;

use DomainException;

final class Polygon
{
    private float $internalAngle;
    private float $externalAngle;
    private float $side;
    private float $radius;

    public function __construct(private int $numberOfSides)
    {
        $this->calculateInternalAngle();
        $this->calculateExternalAngle();
    }

    public function getNumberOfSides(): int
    {
        return $this->numberOfSides;
    }

    public function setSide(float $lado): void
    {
        $this->side = $lado;
        $this->calculateRadius($lado);
    }

    public function setRadius(float $radius): void
    {
        $this->radius = $radius;
        $this->calculateSide($radius);
    }

    public function informarApotema(float $apotema): void
    {
        $raio = $apotema / sin($this->internalAngle / 2);
        $this->setRadius($raio);
    }

    public function apotema(): float
    {
        return sqrt($this->radius ** 2 - ($this->side ** 2) / 4);
    }

    public function altura(): float
    {
        if (0 === $this->numberOfSides % 2) {
            return 2 * $this->apotema();
        }

        return $this->apotema() + $this->radius;
    }

    public function raio(): float
    {
        return $this->radius;
    }

    public function lado(): float
    {
        return $this->side;
    }

    public function maiorDiagonal(): float
    {
        if ($this->numberOfSides <= 3) {
            throw new DomainException('Não é possível calcular para triangulos');
        }

        if (0 === $this->numberOfSides % 2) {
            return 2 * $this->radius;
        }

        return $this->altura() / (sin(($this->internalAngle * ($this->numberOfSides - 1)) / (2 * ($this->numberOfSides - 2))));
    }

    public function anguloInterno(): float
    {
        return $this->internalAngle;
    }

    public function anguloExterno(): float
    {
        return $this->externalAngle;
    }

    public function area(): float
    {
        return $this->perimetro() * $this->apotema() / 2;
    }

    public function perimetro(): float
    {
        return $this->numberOfSides * $this->side;
    }

    private function calculateRadius(): void
    {
        $this->radius = $this->side / (2 * cos($this->internalAngle / 2));
    }

    private function calculateSide(float $raio): void
    {
        $this->side = $raio * 2 * cos($this->internalAngle / 2);
    }

    private function calculateInternalAngle(): void
    {
        $this->internalAngle = M_PI - 2 * M_PI / $this->numberOfSides;
    }

    private function calculateExternalAngle(): void
    {
        $this->externalAngle = 2 * M_PI / $this->numberOfSides;
    }
}
