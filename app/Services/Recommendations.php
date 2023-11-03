<?php

namespace App\Services;

use App\Models\Alert;

class Recommendations
{
    /**
     * @param $alertsForRecommendations
     * @return array
     */
    public static function getListAdvice($alertsForRecommendations): array
    {
        $rules = [
            'c' => [
                Alert::INCREASED => [
                    'a' => 'Investigate reasons for anomalous increase in C (Conversion) and continue in such a way.',
                    'b' => 'Go to l'
                ],
                Alert::DECREASED => [
                    'a' => 'Investigate reasons for anomalous decrease in C (Conversion) and improve it.',
                    'b' => 'Go to l',
                ],
                Alert::UNCHANGED => [
                    'b' => 'Value of metric C (Conversion) was just fine during the last 24 hours, but there is always room for improvement.',
                    'a' => 'Go to l',
                ],
            ],
            'l' => [
                Alert::INCREASED => [
                    'a' => 'Investigate the reasons for anomalous increase in L (Leads) and continue in such a way.',
                    'b' => 'Go to p'
                ],
                Alert::DECREASED => [
                    'a' => 'Investigate the reasons for anomalous decrease in L (Leads) and improve it.',
                    'b' => 'Go to p'
                ],
                Alert::UNCHANGED => [
                    'b' => 'Value of metric L (Leads) was just fine during the last 24 hours, but there is always room for improvement.',
                    'a' => 'Go to p',
                ]
            ],
            'p' => [
                Alert::INCREASED => [
                    'a' => 'Investigate the reasons for anomalous increase in P (Average Check) and continue in such a way.',
                    'b' => 'Go to q'
                ],
                Alert::DECREASED => [
                    'a' => 'Investigate the reasons for anomalous decrease in P (Average Check) and improve it.',
                    'b' => 'Go to q'
                ],
                Alert::UNCHANGED => [
                    'b' => 'Value of metric P (Average Check) was just fine during the last 24 hours, but there is always room for improvement.',
                    'a' => 'Go to q',
                ],
            ],
            'q' => [
                Alert::INCREASED => [
                    'a' => 'Investigate the reasons for anomalous increase in Q (Average Quantity of Orders per Client) and continue in such a way.',
                    'b' => 'Go to car',
                ],
                Alert::DECREASED => [
                    'a' => 'Investigate the reasons for anomalous decrease in Q (Average Quantity of Orders per Client) and improve it.',
                    'b' => 'Go to car',
                ],
                Alert::UNCHANGED => [
                    'b' => 'Value of metric Q (Average Quantity of Orders per Client) was just fine during the last 24 hours, but there is always room for improvement.',
                    'a' => 'Go to car',
                ]
            ],
            'car' => [
                Alert::INCREASED => [
                    'a' => 'Investigate the reasons for anomalous increase in Q (Average Quantity of Orders per Client) and continue in such a way.',
                ],
                Alert::DECREASED => [
                    'a' => 'Investigate the reasons for anomalous decrease in Q (Average Quantity of Orders per Client) and improve it.'
                ],
                Alert::UNCHANGED => [
                    'a' => 'Value of metric Q (Average Quantity of Orders per Client) was just fine during the last 24 hours, but there is always room for improvement.'
                ]
            ]
        ];

        $listAdvice = [];
        foreach ($alertsForRecommendations as $alertForRecommendations) {
            $array = array_reverse($rules[$alertForRecommendations->metric][$alertForRecommendations->result]);
            if ($alertForRecommendations->result === Alert::UNCHANGED) {
                $listAdvice[Alert::UNCHANGED][] = array_pop($array);
            } else {
                $listAdvice[Alert::INCREASED_DECREASED][] = array_pop($array);
            }
        }

        return $listAdvice;
    }
}
