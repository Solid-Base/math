<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Aritimetica;

use DomainException;
use Stringable;

final class Numero implements Stringable
{
    private string $valor;

    public function __construct(int|float|string $numero = 0)
    {
        if (!is_numeric($numero)) {
            throw new DomainException('Não é um numero válido');
        }

        $this->valor = \is_string($numero) ? $numero : $this->converteFloat($numero);
    }

    public function __toString(): string
    {
        return $this->removeFloatStringZeros();
    }

    public function valor(): float
    {
        return (float) $this->valor;
    }

    public function somar(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);
        $this->valor = bcadd($this->valor, $direita);

        return $this;
    }

    public function subtrair(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);
        $this->valor = bcsub($this->valor, $direita);

        return $this;
    }

    public function multiplicar(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);
        $this->valor = bcmul($this->valor, $direita);

        return $this;
    }

    public function modulo(): self
    {
        if ($this->eMenor(0)) {
            return $this->multiplicar(-1);
        }

        return new self($this->valor);
    }

    public function dividir(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);
        $this->valor = bcdiv($this->valor, $direita);

        return $this;
    }

    public function mod(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);
        $this->valor = bcmod($this->valor, $direita);

        return $this;
    }

    public function potencia(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);
        $this->valor = bcpow($this->valor, $direita);

        return $this;
    }

    public function raiz(): self
    {
        $this->valor = bcsqrt($this->valor);

        return $this;
    }

    public function comparar(int|float|Numero $valor): int
    {
        $direita = $this->converteParaNumero($valor);

        return bccomp($this->valor, $direita);
    }

    public function eIgual(int|float|Numero $valor): bool
    {
        $direita = $this->converteParaNumero($valor);

        return 0 === bccomp($this->valor, $direita);
    }

    public function eMaior(int|float|Numero $valor): bool
    {
        $direita = $this->converteParaNumero($valor);

        return 1 === bccomp($this->valor, $direita);
    }

    public function eMenor(int|float|Numero $valor): bool
    {
        $direita = $this->converteParaNumero($valor);

        return -1 === bccomp($this->valor, $direita);
    }

    public function InteiroAcima($numero): self
    {
        if (str_contains($numero, '.')) {
            if (preg_match('~\\.[0]+$~', $numero)) {
                return $this->arredondar(0);
            }
            if ('-' !== $numero[0]) {
                return new self(bcadd($numero, '1', 0));
            }

            return new self(bcsub($numero, '0', 0));
        }

        return new self($numero);
    }

    public function InteiroAbaixo(): self
    {
        $numero = $this->valor;
        if (str_contains($numero, '.')) {
            if (preg_match('~\\.[0]+$~', $numero)) {
                return $this->arredondar(0);
            }
            if ('-' !== $numero[0]) {
                return new self(bcadd($numero, '0', 0));
            }

            return new self(bcsub($numero, '1', 0));
        }

        return new self($numero);
    }

    public function arredondar(int $precisao = 0): self
    {
        $numero = $this->valor;
        if (str_contains($numero, '.')) {
            if ('-' !== $numero[0]) {
                return new self(bcadd($numero, '0.'.str_repeat('0', $precisao).'5', $precisao));
            }

            return new self(bcsub($numero, '0.'.str_repeat('0', $precisao).'5', $precisao));
        }

        return new self($numero);
    }

    private function converteParaNumero(int|float|Numero $valor): string
    {
        if (is_a($valor, self::class)) {
            return (string) $valor;
        }

        return $this->converteFloat($valor);
    }

    private function converteFloat(int|float $valor): string
    {
        $norm = (string) ($valor);

        if (($e = mb_strrchr($norm, 'E')) === false) {
            return $norm;
        }
        $decimal = (-(int) (mb_substr($e, 1))) < 0 ? 0 : (-(int) (mb_substr($e, 1)));

        return number_format($valor, $decimal, '.', '');
    }

    private function removeFloatStringZeros(): string
    {
        $patterns = ['/[\.][0]+$/', '/([\.][0-9]*[1-9])([0]*)$/'];
        $replaces = ['', '$1'];

        return preg_replace($patterns, $replaces, $this->valor);
    }
}
