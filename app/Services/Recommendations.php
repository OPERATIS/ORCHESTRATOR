<?php

namespace App\Services;

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
                'Increased' => [
                    'a' => 'Recommend: Investigate reasons for unsystematic increase in C and continue in such a way.',
                    'b' => 'Go to l'
                ],
                'Decreased' => [
                    'a' => 'Recommend: Investigate reasons for unsystematic decrease in C and improve it (via refining sales process, enhancing user experience, etc.)',
                    'b' => 'Go to l',
                ]
            ],
            'l' => [
                'Increased' => [
                    'a' => 'Recommend: Investigate the reasons for unsystematic increase in L and continue in such a way.',
                    'b' => 'Go to p'
                ],
                'Decreased' => [
                    'a' => 'Recommend: Investigate the reasons for unsystematic decrease in L and improve it (via marketing, targeting, etc.).',
                    'b' => 'Go to p'
                ]
            ],
            'p' => [
                'Increased' => [
                    'a' => 'Recommend: Investigate the reasons for unsystematic increase in P and continue in such a way.',
                    'b' => 'Go to q'
                ],
                'Decreased' => [
                    'a' => 'Recommend: Investigate the reasons for unsystematic decrease in P and improve it (via pricing strategy, product quality, etc.)',
                    'b' => 'Go to q'
                ]
            ],
            'q' => [
                'Increased' => [
                    'a' => 'Recommend: Investigate the reasons for unsystematic increase in Q and continue in such a way.',
                ],
                'Decreased' => [
                    'a' => 'Recommend: Investigate the reasons for unsystematic decrease in Q and improve it (via customer retention, bundle discounts, etc.)'
                ]
            ]
        ];

        $listAdvice = [];
        foreach ($alertsForRecommendations as $alertForRecommendations) {
            foreach ($rules[$alertForRecommendations->metric][$alertForRecommendations->result] as $item) {
                if (!str_contains($item, 'Go to')) {
                    $listAdvice[$alertForRecommendations->metric] = $item;
                }
            }
        }

        return $listAdvice;
    }
}
