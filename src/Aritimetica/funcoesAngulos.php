<?php

declare(strict_types=1);

if (!function_exists('normatizarAngulo')) {
    /**
     * Retorna o angulo no intervalo 0, 2PI;
     *
     * @param float $angulo em radianos
     * @return float angulo em 0 e 2PI radianos
     */
    function normatizarAngulo(float $angulo): float
    {
        $anguloRetorno = fmod($angulo, 2 * M_PI);

        return eMenor($anguloRetorno, 0) ? $anguloRetorno + 2 * M_PI : $anguloRetorno;
    }
}
if (!function_exists('anguloTexto')) {
    /**
     * Retorna o angulo de rotação do texto que o tornal legivel;
     *
     * @param float $angulo em radianos
     * @return float angulo em 0 e 2PI radianos
     */
    function anguloTexto(float $angulo): float
    {
        $retorno = normatizarAngulo($angulo);
        if (entre(M_PI / 2, $retorno, M_PI, false)) {
            return $retorno + M_PI;
        }
        if (entre(M_PI, $retorno, 3 * M_PI / 2)) {
            return $retorno - M_PI;
        }

        return $retorno;
    }
}

if (!function_exists('grau')) {
    function grau(float $anguloRad): float
    {
        return rad2deg($anguloRad);
    }
}
if (!function_exists('rad')) {
    function rad(float $anguloGrau): float
    {
        return deg2rad($anguloGrau);
    }
}
