<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Aritimetica;

use DomainException;
use Stringable;

final class Numero implements Stringable
{
    public readonly int $precisao;
    private string $valor;

    public function __construct(int|float|string $numero = 0, ?int $precisao = null)
    {
        if (!is_numeric($numero)) {
            throw new DomainException('Não é um numero válido');
        }
        $valor = $this->converteFloat($numero);
        $precisaoBc = $this->obtenhaBcPrecisao($valor);
        $this->precisao = $precisao ?? max($precisaoBc, bcscale());
        $this->valor = $valor;
    }

    public function __toString(): string
    {
        $valor = $this->numeroArredondado($this->precisao, $this->valor);

        return $this->removeFloatStringZeros($valor);
    }

    public function valor(): float
    {
        return (float) $this->numeroArredondado($this->precisao, $this->valor);
    }

    public function somar(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);
        $this->valor = bcadd($this->valor, $direita, $this->precisao);

        return $this;
    }

    public function subtrair(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);

        $this->valor = bcsub($this->valor, $direita, $this->precisao);

        return $this;
    }

    public function multiplicar(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);
        $this->valor = bcmul($this->valor, $direita, $this->precisao);

        return $this;
    }

    public function modulo(): self
    {
        if ($this->eMenor(0)) {
            return $this->multiplicar(-1);
        }

        return new self($this->valor, $this->precisao);
    }

    public function dividir(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);

        $this->valor = bcdiv($this->valor, $direita, $this->precisao);

        return $this;
    }

    public function mod(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);
        $this->valor = bcmod($this->valor, $direita, $this->precisao);

        return $this;
    }

    public function potencia(int|float|Numero $valor): self
    {
        $direita = $this->converteParaNumero($valor);
        $precisao = $this->precisao + $this->obtenhaBcPrecisao($direita);
        $this->valor = bcpow($this->valor, $direita, $precisao);

        return $this;
    }

    public function raiz(): self
    {
        $this->valor = bcsqrt($this->valor, 2 * $this->precisao);

        return $this;
    }

    public function comparar(int|float|Numero $valor): int
    {
        $direita = $this->converteParaNumero($valor);

        return bccomp($this->valor, $direita, $this->precisao);
    }

    public function eIgual(int|float|Numero $valor): bool
    {
        $direita = $this->converteParaNumero($valor);

        return 0 === bccomp($this->valor, $direita, $this->precisao);
    }

    public function eMaior(int|float|Numero $valor): bool
    {
        $direita = $this->converteParaNumero($valor);

        return 1 === bccomp($this->valor, $direita, $this->precisao);
    }

    public function eMenor(int|float|Numero $valor): bool
    {
        $direita = $this->converteParaNumero($valor);

        return -1 === bccomp($this->valor, $direita, $this->precisao);
    }

    public function InteiroAcima(): self
    {
        $numero = $this->valor;
        if (str_contains($numero, '.')) {
            if (preg_match('~\\.[0]+$~', $numero)) {
                return $this->arredondar(0);
            }
            if ('-' !== $numero[0]) {
                return new self(bcadd($numero, '1', 0), 0);
            }

            return new self(bcsub($numero, '0', 0), 0);
        }

        return new self($numero, 0);
    }

    public function InteiroAbaixo(): self
    {
        $numero = $this->valor;
        if (str_contains($numero, '.')) {
            if (preg_match('~\\.[0]+$~', $numero)) {
                return $this->arredondar(0);
            }
            if ('-' !== $numero[0]) {
                return new self(bcadd($numero, '0', 0), 0);
            }

            return new self(bcsub($numero, '1', 0), 0);
        }

        return new self($numero, 0);
    }

    public function inteiro(): self
    {
        return $this->arredondar(0);
    }

    public function arredondar(int $precisao = 0): self
    {
        $numero = $this->numeroArredondado($precisao, $this->valor);

        return new self($numero, $precisao);
    }

    private function numeroArredondado(int $precisao, string $numero): string
    {
        $precisao = $precisao < 0 ? 0 : $precisao;
        $precisaoTotal = $this->obtenhaBcPrecisao($numero);
        while ($precisaoTotal >= $precisao) {
            $t = '0.'.str_repeat('0', $precisaoTotal).'5';
            $numero = (float) $numero < 0 ? bcsub($numero, $t, $precisaoTotal) : bcadd($numero, $t, $precisaoTotal);
            --$precisaoTotal;
        }

        return $numero;
    }

    private function obtenhaBcPrecisao(string $numero): int
    {
        $pontoPrecisao = mb_strpos($numero, '.');
        if (false === $pontoPrecisao) {
            return 0;
        }

        return mb_strlen($numero) - mb_strpos($numero, '.') - 1;
    }

    private function converteParaNumero(int|float|Numero $valor): string
    {
        if (is_a($valor, self::class)) {
            return (string) $valor;
        }

        return $this->converteFloat($valor);
    }

    private function converteFloat(int|float|string $valor): string
    {
        if (is_string($valor) && false === mb_strrchr($valor, 'E')) {
            return $valor;
        }
        $norm = (string) ($valor);

        if (($e = mb_strrchr($norm, 'E')) === false) {
            return $norm;
        }
        $decimal = (-(int) (mb_substr($e, 1))) < 0 ? 0 : (-(int) (mb_substr($e, 1)));

        return number_format((float) $valor, $decimal, '.', '');
    }

    private function removeFloatStringZeros(string $valor): string
    {
        $patterns = ['/[\.][0]+$/', '/([\.][0-9]*[1-9])([0]*)$/'];
        $replaces = ['', '$1'];

        return preg_replace($patterns, $replaces, $valor);
    }
}
