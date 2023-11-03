<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    public const PERIOD_HOUR = '1_hour';
    public const PERIOD_DAY = '1_day';

    public const METRICS = [self::L, self::C, self::P, self::Q, self::CAR];

    public const SUB_METRICS = [
        self::L => [
            'default' => [self::L_PRCR, self::L_MAP, self::L_CPD],
            'custom' => [self::L_CSR, self::L_WCCR,]
        ],
        self::C => [
            'default' => [self::C_CCR, self::C_CAR, self::C_GRCCR, self::C_PMD],
            'custom' => [self::C_CER, self::C_PGFR, self::C_GCRAC, self::C_CSRR],
        ],
        self::P => [
            'default' => [self::P_AOV, self::P_DUR, self::P_CCUR, self::P_MPPR, self::P_CV],
            'custom' => [self::P_CSR, self::P_USR, self::P_PRCR,],
        ],
        self::Q => [
            'default' => [self::Q_GCUR,],
            'custom' => [self::Q_RR, self::Q_LPCR, self::Q_ECUR,],
        ],
        self::CAR => [
            'default' => [self::CAR_CT, self::CAR_PMU, self::CAR_TTP, self::CAR_CRR, self::CAR_TTV],
            'custom' => [self::CAR_SSDR, self::CAR_OSRC, self::CAR_FPR, self::CAR_MCCB],
        ],
    ];

    public const L = 'l';
    public const L_PRCR = 'l_prcp';
    public const L_CSR = 'l_csr';
    public const L_WCCR = 'l_wccr';
    public const L_MAP = 'l_map';
    public const L_CPD = 'l_cpd';

    public const C = 'c';
    public const C_CCR = 'c_ccr';
    public const C_CAR = 'c_car';
    public const C_CER = 'c_cer';
    public const C_PGFR = 'c_pgfr';
    public const C_GRCCR = 'c_grccr';
    public const C_GCRAC = 'c_gcrac';
    public const C_PMD = 'c_pmd';
    public const C_CSRR = 'c_csrr';

    public const P = 'p';
    public const P_AOV = 'p_aov';
    public const P_DUR = 'p_dur';
    public const P_CSR = 'p_csr';
    public const P_USR = 'p_usr';
    public const P_CCUR = 'p_ccur';
    public const P_PRCR = 'p_prcp';
    public const P_MPPR = 'p_mppr';
    public const P_CV = 'p_cv';

    public const Q = 'q';
    public const Q_RR = 'q_rr';
    public const Q_LPCR = 'q_lpcr';
    public const Q_GCUR = 'q_gcur';
    public const Q_ECUR = 'q_ecur';

    public const CAR = 'car';
    public const CAR_CT = 'car_ct';
    public const CAR_SSDR = 'car_ssdr';
    public const CAR_PMU = 'car_pmu';
    public const CAR_TTP = 'car_ttp';
    public const CAR_OSRC = 'car_osrc';
    public const CAR_CRR = 'car_crr';
    public const CAR_FPR = 'car_fpr';
    public const CAR_MCCB = 'car_mccb';
    public const CAR_TTV = 'car_ttv';

    public const NAMES = [
        self::L => 'Leads',
        self::C => 'Conversion Rate',
        self::P => 'Average Check',
        self::Q => 'Average Quantity of Orders per Client',
        self::CAR => 'Cart Abandonment Rate',
    ];

    protected $guarded = [];
    public $dates = [
        'start_period',
        'end_period'
    ];
    protected $casts = [
        'l' => 'float',
        'c' => 'float',
        'p' => 'float',
        'q' => 'float',
        'car' => 'float',
        'l_map' => 'json',
        'c_pmd' => 'json',
        'car_pmu' => 'json'
    ];

    /**
     * @param Builder $query
     * @param $period
     */
    public function scopePeriod(Builder $query, $period)
    {
        $query->where('period', $period);
    }
}
