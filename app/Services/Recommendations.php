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
                    'a' => 'Investigate reasons for unsystematic increase in C and continue in such a way.',
                    'b' => 'Go to l'
                ],
                Alert::DECREASED => [
                    'a' => 'Investigate reasons for unsystematic decrease in C and improve it (via refining sales process, enhancing user experience, etc.)',
                    'b' => 'Go to l',
                ],
                Alert::UNCHANGED => [
                    'b' => 'Value of metric C (Conversion) was just fine during the last 24 hours, but there is always room for improvement.',
                    'a' => 'Go to l',
                ],
            ],
            'l' => [
                Alert::INCREASED => [
                    'a' => 'Investigate the reasons for unsystematic increase in L and continue in such a way.',
                    'b' => 'Go to p'
                ],
                Alert::DECREASED => [
                    'a' => 'Investigate the reasons for unsystematic decrease in L and improve it (via marketing, targeting, etc.).',
                    'b' => 'Go to p'
                ],
                Alert::UNCHANGED => [
                    'b' => 'Value of metric L (Leads) was just fine during the last 24 hours, but there is always room for improvement.',
                    'a' => 'Go to p',
                ]
            ],
            'p' => [
                Alert::INCREASED => [
                    'a' => 'Investigate the reasons for unsystematic increase in P and continue in such a way.',
                    'b' => 'Go to q'
                ],
                Alert::DECREASED => [
                    'a' => 'Investigate the reasons for unsystematic decrease in P and improve it (via pricing strategy, product quality, etc.)',
                    'b' => 'Go to q'
                ],
                Alert::UNCHANGED => [
                    'b' => 'Value of metric P (Average Check) was just fine during the last 24 hours, but there is always room for improvement.',
                    'a' => 'Go to q',
                ],
            ],
            'q' => [
                Alert::INCREASED => [
                    'a' => 'Investigate the reasons for unsystematic increase in Q and continue in such a way.',
                ],
                Alert::DECREASED => [
                    'a' => 'Investigate the reasons for unsystematic decrease in Q and improve it (via customer retention, bundle discounts, etc.)'
                ],
                Alert::UNCHANGED => [
                    'a' => 'Value of metric Q (Average Purchases Number) was just fine during the last 24 hours, but there is always room for improvement.'
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
