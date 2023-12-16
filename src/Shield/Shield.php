<?php

declare(strict_types=1);

namespace SolidBase\Math\Shield;

use Solidbase\Geometria\Dominio\Ponto;
use SolidBase\Math\Algebra\Matriz;
use SolidBase\Math\Algebra\InverseMatrix;

class Shield
{
    private Matriz $p;
    private Matriz $s;
    private Matriz $deslocamento;
    private Matriz $normal;

    public function __construct(
        private array $pontos,
        private Matriz $rigidez
    ) {}

    public function informarRigidez(Matriz $rigidez): void
    {
        $this->rigidez = $rigidez;
    }

    public function Calcular(Matriz $matrizR): Matriz
    {
        $this->gerarMatrizP();
        $this->matrizS();
        if ($this->p->getM() !== $matrizR->getN()) {
            $novo = [];
            $max = $this->p->getN();
            for ($i = 1; $i <= $max; ++$i) {
                $novo[$i - 1] = [$matrizR["{$i}"]];
            }
            $matrizR = new Matriz($novo);
        }
        $inversa = InverseMatrix::Inverse($this->s);
        $nu = $inversa->multiply($matrizR)->transpose();
        $this->deslocamento = $nu;
        $normal = $nu->multiply($this->p)->multiply($this->rigidez);
        $this->normal = $normal;

        return $normal;
    }

    public function Deslocamentos(): Matriz
    {
        return $this->deslocamento;
    }

    private function gerarMatrizP(): void
    {
        $matriz = [];

        /** @var Ponto $ponto */
        foreach ($this->pontos as $key => $ponto) {
            $px = 1;
            $py = 0;
            $pz = 0;
            $pa = 0;
            $pb = sbNormalize($ponto->x);
            $pc = sbNormalize($ponto->y * -1);
            $matriz[0][$key] = $px;
            // $matriz[1][$key] = $py;
            // $matriz[2][$key] = $pz;
            // $matriz[3][$key] = $pa;
            $matriz[1][$key] = $pb;
            $matriz[2][$key] = $pc;
        }

        foreach ($matriz as $key => $linha) {
            $soma = array_reduce($linha, fn(float $total, float|int $n) => $total + abs($n), 0);
            if (sbIsZero($soma)) {
                unset($matriz[$key]);
            }
        }
        $this->p = new Matriz($matriz);
    }

    private function matrizS(): void
    {
        $matriz = $this->p->multiply($this->rigidez)->multiply($this->p->transpose());
        $this->s = $matriz;
        // $matriz = [];
        // $numero = $this->p->obtenhaM();
        // for ($i = 0; $i < $numero; ++$i) {
        //     $linha_i = $this->p->obtenhaLinha($i);
        //     $linha_i = $linha_i->Transposta();
        //     for ($j = $i; $j < $numero; ++$j) {
        //         $linha_j = $this->p->obtenhaLinha($j);
        //         $linhaj = $this->rigidez->Multiplicar($linha_j);
        //         $multiplicacao = $linha_i->Multiplicar($linhaj);
        //         $matriz[$i][$j] = $matriz[$j][$i] = $multiplicacao[0][0];
        //     }
        // }
        // $this->s = new Matriz($matriz);
    }
}
