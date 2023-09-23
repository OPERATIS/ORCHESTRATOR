<?php

namespace App\Services;

class SixSigma
{
    public const METRICS = ['c', 'l', 'p', 'q'];

    /**
     * @param $arrays
     * @return float|int
     */
    public function getMean($arrays)
    {
        return array_sum($arrays) / count($arrays);
    }

    /**
     * @param $arrays
     * @param $mean
     * @return float
     */
    public function getStandardDeviation($arrays, $mean): float
    {
        $deviationFromTheMean = [];
        foreach ($arrays as $item) {
            $deviationFromTheMean[] = pow($item - $mean, 2);
        }

        $findTheVariance = array_sum($deviationFromTheMean) / (count($deviationFromTheMean) - 1);
        return sqrt($findTheVariance);
    }

    /**
     * @param $mean
     * @param $standardDeviation
     * @return float|int
     */
    public function getUCL($mean, $standardDeviation)
    {
        return $mean + 3 * $standardDeviation;
    }

    /**
     * @param $mean
     * @param $standardDeviation
     * @return float|int
     */
    public function getLCL($mean, $standardDeviation)
    {
        return $mean - 3 * $standardDeviation;
    }

    public function getCLs($arrays): array
    {
        $mean = $this->getMean($arrays);
        $standardDeviation = $this->getStandardDeviation($arrays, $mean);
        return [
            'ucl' => $this->getUCL($mean, $standardDeviation),
            'lcl' => $this->getLCL($mean, $standardDeviation)
        ];
    }
}
