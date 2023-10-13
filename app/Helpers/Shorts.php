<?php

namespace App\Helpers;


class Shorts
{
    public static function formatNumber($n, $mode = null, $decimals = 2, $ceil_small_numbers = true)
    {
        if (is_null($n)) {
            return null;
        } elseif (is_string($n)) {
            $n = intval($n);
        }
        $value = 0;
        $num = abs($n);

        if ($mode == 'full') {
            $value = number_format($num, ($decimals > 0 && floor($num) != $num ? $decimals : 0), '.', ' ');
        }elseif ($num >= 1000000000000) {
            $suffix = 'T';
            $value = number_format(($num / 1000000000000), $decimals, '.', ' ');
        } elseif ($num >= 1000000000) {
            $suffix = 'B';
            $value = number_format(($num / 1000000000), $decimals, '.', ' ');
        } elseif ($num >= 1000000) {
            $suffix = 'M';
            $value = number_format(($num / 1000000), $decimals, '.', ' ');
        } elseif ($num >= 1000) {
            $suffix = 'K';
            $value = number_format(($num / 1000), $decimals != 2 ? $decimals : 0, '.', ' ');
        } else {
            $suffix = '';
            $value = number_format($num, $decimals, '.', ' ');
        }

        if (isset($suffix)) {
            if (floor($value) == $value && $ceil_small_numbers) {
                $value = floor($value);
            }
            $value .= $suffix;
        }

        return $n < 0 ? '-' . $value : $value;
    }
}
