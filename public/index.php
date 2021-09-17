<?php

declare(strict_types=1);

use Solidbase\Geometria\Dominio\Ponto;
use SolidBase\Matematica\Algebra\FabricaMatriz;
use SolidBase\Matematica\Algebra\Matriz;
use SolidBase\Matematica\Shield\Shield;

include '../vendor/autoload.php';

$array = [new Ponto(0, .5774), new Ponto(0.5, -.2887), new Ponto(-0.5, -.2887)];

$rigidez = (2380000 * 0.12566) / 4;

$matrizRigidez = FabricaMatriz::Identidade(3)->Escalar($rigidez);

$shield = new Shield($array, $matrizRigidez);

$resposta = $shield->Calcular(new Matriz([[300], [20], [0]]));

print_r($shield->Deslocamentos());
