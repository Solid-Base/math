<?php

declare(strict_types=1);

if (!function_exists('sbNormalizeAngle')) {
    /**
     * Retorna o angulo no intervalo 0, 2PI;
     *
     * @param float $angle em radianos
     * @return float angulo em 0 e 2PI radianos
     */
    function sbNormalizeAngle(float $angle): float
    {
        $angleReturn = fmod($angle, 2 * M_PI);

        return sbLessThan($angleReturn, 0) ? $angleReturn + 2 * M_PI : $angleReturn;
    }
}
if (!function_exists('sbAngleText')) {
    /**
     * Retorna o angulo de rotação do texto que o tornal legivel;
     *
     * @param float $angle em radianos
     * @return float angulo em 0 e 2PI radianos
     */
    function sbAngleText(float $angle): float
    {
        $result = sbNormalizeAngle($angle);
        if (sbBetween(M_PI / 2, $result, M_PI, false)) {
            return $result + M_PI;
        }
        if (sbBetween(M_PI, $result, 3 * M_PI / 2)) {
            return $result - M_PI;
        }

        return $result;
    }
}

if (!function_exists('sbDegree')) {
    function sbDegree(float $angleRad): float
    {
        return rad2deg($angleRad);
    }
}
if (!function_exists('sbRad')) {
    function sbRad(float $angleDegree): float
    {
        return deg2rad($angleDegree);
    }
}
