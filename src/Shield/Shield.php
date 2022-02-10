<?php

declare(strict_types=1);

namespace SolidBase\Matematica\Shield;

use Solidbase\Geometria\Dominio\Ponto;
use SolidBase\Matematica\Algebra\Matriz;
use SolidBase\Matematica\Algebra\MatrizInversa;
use SolidBase\Matematica\Aritimetica\Numero;

class Shield
{
    private Matriz $p;
    private Matriz $s;
    private Matriz $deslocamento;
    private Matriz $normal;

    public function __construct(
        private array $pontos,
        private Matriz $rigidez
    ) {
    }

    // private function __gerarRigidez(): void
    // {
    //     $numero = \count($this->estacas);
    //     $estaca = $this->estacas[0];
    //     $concreto = Concreto::Criar($estaca->obtenhaCriterio());
    //     $diametro = $estaca->obtenhaDiametro();
    //     $area = Circulo::Area($diametro);
    //     $comprimento = $estaca->obtenhaComprimento();
    //     $rigidez = ($concreto->ecs * $area) / $comprimento;
    //     $identidade = FabricaMatriz::Identidade($numero)->Escalar($rigidez);
    //     $this->rigidez = $identidade;
    // }

    private function __gerarMatrizP(): void
    {
        $matriz = [];
        /** @var Ponto $ponto */
        foreach ($this->pontos as $key => $ponto) {
            $px = numero(1, PRECISAO_SOLIDBASE);
            $py = numero(0, PRECISAO_SOLIDBASE);
            $pz = numero(0, PRECISAO_SOLIDBASE);
            $pa = numero(0, PRECISAO_SOLIDBASE);
            $pb = $ponto->x;
            $pc = $ponto->y->multiplicar(-1);
            $matriz[0][$key] = $px;
            // $matriz[1][$key] = $py;
            // $matriz[2][$key] = $pz;
            // $matriz[3][$key] = $pa;
            $matriz[1][$key] = $pb;
            $matriz[2][$key] = $pc;
        }

        foreach ($matriz as $key => $linha) {
            $soma = array_reduce($linha, fn (float $total, Numero $n) => $total + modulo($n)->valor(), 0);
            if (eZero($soma)) {
                unset($matriz[$key]);
            }
        }
        $this->p = new Matriz($matriz);
    }

    private function __matrizS(): void
    {
        $matriz = [];
        $numero = $this->p->obtenhaM();
        for ($i = 0; $i < $numero; ++$i) {
            $linha_i = new Matriz($this->p->obtenhaLinha($i));
            $linha_i = $linha_i->Transposta();
            for ($j = $i; $j < $numero; ++$j) {
                $linha_j = new Matriz($this->p->obtenhaLinha($j));
                $linhaj = $this->rigidez->Multiplicar($linha_j);
                $multiplicacao = $linha_i->Multiplicar($linhaj);
                $matriz[$i][$j] = $matriz[$j][$i] = $multiplicacao[0][0];
            }
        }
        $this->s = new Matriz($matriz);
    }

    public function informarRigidez(Matriz $rigidez): void
    {
        $this->rigidez = $rigidez;
    }

    public function Calcular(Matriz $matrizR): Matriz
    {
        $this->__gerarMatrizP();
        $this->__matrizS();
        if ($this->p->obtenhaM() !== $matrizR->obtenhaM()) {
            $novo = [];
            $max = $this->p->obtenhaM();
            for ($i = 0; $i < $max; ++$i) {
                $novo[$i] = [$matrizR[$i][0]];
            }
            $matrizR = new Matriz($novo);
        }
        $inversa = MatrizInversa::Inverter($this->s);
        $nu = $inversa->Multiplicar($matrizR)->Transposta();
        $this->deslocamento = $nu;
        $normal = $nu->Multiplicar($this->p)->Multiplicar($this->rigidez);
        $this->normal = $normal;

        return $normal;
    }

    public function Deslocamentos(): Matriz
    {
        return $this->deslocamento;
    }
}
