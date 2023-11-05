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

    public const DESCRIPTIONS = [
        self::L => [
            'symbol' => 'L',
            'name' => 'Leads',
            'description' => 'Leads',
            'definition' => 'How many users came to our website and added at least one product to the shopping cart.',
            'sub_metrics' => [
                'default' => [self::L_PRCR, self::L_MAP, self::L_CPD],
                'custom' => [self::L_CSR, self::L_WCCR,]
            ],
        ],
        self::L_PRCR => [
            'symbol' => 'PRCR',
            'name' => 'Product Recommendations Conversion Rate',
            'description' => 'Product Recommendations Conversion Rate',
            'definition' => 'The ratio of the number of times a recommended product is added to the cart to the number of times recommendations are displayed.',
            'dataRequirement' => 'Number of product recommendations displayed, number of times a recommended product is added to the cart.',
        ],
        self::L_WCCR => [
            'symbol' => 'WCCR',
            'name' => 'Wishlist to Cart Conversion Rate',
            'description' => 'Wishlist to Cart Conversion Rate',
            'definition' => 'The ratio of items added to the cart from the wishlist to the total number of items in wishlists.',
            'dataRequirement' => 'Total number of items in wishlists, number of wishlist items added to the cart.',
        ],
        self::L_MAP => [
            'symbol' => 'MAP',
            'name' => 'Most Abandoned Product',
            'description' => 'Most Abandoned Product',
            'definition' => 'The product that is most frequently added to carts but not purchased.',
            'dataRequirement' => 'Abandonment rate for each product (number of times added to cart but not purchased).',
        ],
        self::L_CPD => [
            'symbol' => 'CPD',
            'name' => 'Cart Product Diversity',
            'description' => 'Cart Product Diversity',
            'definition' => 'The variety of different products in a cart.',
            'dataRequirement' => 'Types of products in the cart.',
        ],

        self::C => [
            'symbol' => 'C',
            'name' => 'Conversion',
            'description' => 'Conversion from Leads to Purchase/Transactions',
            'definition' => 'Ratio of number of unique users who made purchases/transactions to how many users came to our website and added at least one product to the shopping cart.',
            'sub_metrics' => [
                'default' => [self::C_CCR, self::C_CAR, self::C_GRCCR, self::C_PMD],
                'custom' => [self::C_CER, self::C_PGFR, self::C_GCRAC, self::C_CSRR],
            ],
        ],
        self::C_CCR => [
            'symbol' => 'CCR',
            'name' => 'Checkout Conversion Rate',
            'description' => 'Checkout Conversion Rate',
            'definition' => 'The ratio of successful purchases to the number of initiated checkouts.',
            'dataRequirement' => 'Total number of checkouts initiated, total number of successful purchases.',
        ],
        self::C_CAR => [
            'symbol' => 'CAR',
            'name' => 'Abandoned Cart Rate',
            'description' => 'Abandoned Cart Rate',
            'definition' => 'The ratio of carts that are abandoned to the total number of carts created.',
            'dataRequirement' => 'Total number of carts created, number of carts abandoned.',
        ],
        self::C_CER => [
            'symbol' => 'CER',
            'name' => 'Checkout Error Rate',
            'description' => 'Checkout Error Rate',
            'definition' => 'The ratio of checkout attempts that result in an error to the total number of checkout attempts.',
            'dataRequirement' => 'Total number of checkout attempts, number of checkout errors.',
        ],
        self::C_PGFR => [
            'symbol' => 'PGFR',
            'name' => 'Payment Gateway Fail Rate',
            'description' => 'Payment Gateway Fail Rate',
            'definition' => 'The ratio of failed payment gateway transactions to the total number of attempted transactions.',
            'dataRequirement' => 'Total number of payment attempts, number of failed payments.',
        ],
        self::C_GRCCR => [
            'symbol' => 'GRCCR',
            'name' => 'Guest vs. Registered Checkout Rate',
            'description' => 'Guest vs. Registered Checkout Rate',
            'definition' => 'The comparison of checkouts initiated by guest users versus registered users.',
            'dataRequirement' => 'Total number of guest checkouts, total number of registered user checkouts.',
        ],
        self::C_GCRAC => [
            'symbol' => 'GCRAC',
            'name' => 'Rate of Guest Checkouts Leading to Account Creation',
            'description' => 'Guest vs. Registered Checkout Rate',
            'definition' => 'The ratio of guest checkouts that result in new account creation to the total number of guest checkouts.',
            'dataRequirement' => 'Number of new accounts created post-checkout, total number of guest checkouts.',
        ],
        self::C_PMD => [
            'symbol' => 'PMD',
            'name' => 'Payment Methods Distribution',
            'description' => 'Payment Methods Distribution',
            'definition' => 'The distribution of different payment methods used in transactions.',
            'dataRequirement' => 'Usage count of each payment method.',
        ],
        self::C_CSRR => [
            'symbol' => 'CSRR',
            'name' => 'Checkout Support Request Rate',
            'description' => 'Checkout Support Request Rate',
            'definition' => 'The ratio of customer support requests during the checkout process to the total number of checkout attempts.',
            'dataRequirement' => 'Number of support requests, total number of checkout attempts.',
        ],

        self::P => [
            'symbol' => 'P',
            'name' => 'Average Order Value',
            'description' => 'Average Order Value',
            'definition' => 'Ratio of total Orders value to number of purchases',
            'sub_metrics' => [
                'default' => [self::P_AOV, self::P_DUR, self::P_CCUR, self::P_MPPR, self::P_CV],
                'custom' => [self::P_CSR, self::P_USR, self::P_PRCR,],
            ],
        ],
        self::P_AOV => [
            'symbol' => 'P',
            'name' => 'Average Order Value',
            'description' => 'Average Order Value',
            'definition' => 'The average amount spent each time a customer places an order on a website.',
            'dataRequirement' => 'Total revenue, total number of orders.',
        ],
        self::P_DUR => [
            'symbol' => 'DUR',
            'name' => 'Discount Usage Rate',
            'description' => 'Discount Usage Rate',
            'definition' => 'The ratio of orders using a discount to the total number of orders.',
            'dataRequirement' => 'Number of orders using discounts, total number of orders.',
        ],
        self::P_CSR => [
            'symbol' => 'CSR',
            'name' => 'Cross-Sell Rate',
            'description' => 'Cross-Sell Rate',
            'definition' => 'The ratio of orders containing cross-sell or upsell items to the total number of orders.',
            'dataRequirement' => 'Number of orders with cross-sell or upsell, total number of orders.',
        ],
        self::P_USR => [
            'symbol' => 'USR',
            'name' => 'Upsell Rate',
            'description' => 'Upsell Rate',
            'definition' => 'The ratio of orders containing cross-sell or upsell items to the total number of orders.',
            'dataRequirement' => 'Number of orders with cross-sell or upsell, total number of orders.',
        ],
        self::P_CCUR => [
            'symbol' => 'CCUR',
            'name' => 'Coupon Code Usage Rate',
            'description' => 'Coupon Code Usage Rate',
            'definition' => 'The ratio of orders using coupon codes to the total number of orders.',
            'dataRequirement' => 'Number of orders using coupon codes, total number of orders.',
        ],
        self::P_PRCR => [
            'symbol' => 'PRCR',
            'name' => 'Product Recommendations Conversion Rate',
            'description' => 'Product Recommendations Conversion Rate',
            'definition' => 'The ratio of purchases of recommended products to the total number of product recommendations.',
            'dataRequirement' => 'Number of recommended products purchased, total number of product recommendations.',
        ],
        self::P_MPPR => [
            'symbol' => 'MPPR',
            'name' => 'Multiple Product Purchase Rate',
            'description' => 'Multiple Product Purchase Rate',
            'definition' => 'The ratio of orders containing more than one product to the total number of orders.',
            'dataRequirement' => 'Number of orders with multiple products, total number of orders.',
        ],
        self::P_CV => [
            'symbol' => 'CV',
            'name' => 'Cart Value',
            'description' => 'Cart Value',
            'definition' => 'The total value of all products within active carts.',
            'dataRequirement' => 'Value of products in all active carts.',
        ],

        self::Q => [
            'symbol' => 'Q',
            'name' => 'Average number of purchases per client',
            'description' => 'Average number of purchases per client',
            'definition' => 'Ratio of total Orders number to number of unique clients',
            'sub_metrics' => [
                'default' => [self::Q_GCUR,],
                'custom' => [self::Q_RR, self::Q_LPCR, self::Q_ECUR,],
            ],
        ],
        self::Q_RR => [
            'symbol' => 'RR',
            'name' => 'Return Rate',
            'description' => 'Return Rate',
            'definition' => 'The ratio of returned purchases to the total number of purchases.',
            'dataRequirement' => 'Total number of purchases, number of purchases returned.',
        ],
        self::Q_LPCR => [
            'symbol' => 'LPCR',
            'name' => 'Loyalty Program Conversion Rate',
            'description' => 'Loyalty Program Conversion Rate',
            'definition' => 'The ratio of customers who make a purchase and join the loyalty program to all customers who make a purchase.',
            'dataRequirement' => 'Total number of customers, number of customers joining the loyalty program after purchase.',
        ],
        self::Q_GCUR => [
            'symbol' => 'GCUR',
            'name' => 'Gift Card Usage Rate',
            'description' => 'Gift Card Usage Rate',
            'definition' => 'The ratio of transactions using a gift card to the total number of transactions.',
            'dataRequirement' => 'Total number of transactions, number of transactions using gift cards.',
        ],
        self::Q_ECUR => [
            'symbol' => 'ECUR',
            'name' => 'Express Checkout Usage Rate',
            'description' => 'Express Checkout Usage Rate',
            'definition' => 'The ratio of users who utilize express checkout options to the total number of checkouts.',
            'dataRequirement' => 'Total number of checkouts, number of checkouts using express checkout.',
        ],

        self::CAR => [
            'symbol' => 'CAR',
            'name' => 'Cart Abandonment Rate',
            'description' => 'Cart Abandonment Rate',
            'definition' => 'The ratio of carts that are abandoned to the total number of carts created.',
            'sub_metrics' => [
                'default' => [self::CAR_CT, self::CAR_PMU, self::CAR_TTP, self::CAR_CRR, self::CAR_TTV],
                'custom' => [self::CAR_SSDR, self::CAR_OSRC, self::CAR_FPR, self::CAR_MCCB],
            ],
        ],
        self::CAR_CT => [
            'symbol' => 'CT',
            'name' => 'Checkout Time',
            'description' => 'Checkout Time',
            'definition' => 'The amount of time it takes for a customer to complete the checkout process, from beginning to end.',
            'dataRequirement' => 'Timestamp when a customer starts the checkout process and when they complete it.',
        ],
        self::CAR_SSDR => [
            'symbol' => 'SSDR',
            'name' => 'Shipping Selection Dropoff Rate',
            'description' => 'Shipping Selection Dropoff Rate',
            'definition' => 'The percentage of customers who abandon their carts after reaching the shipping selection stage of the checkout process.',
            'dataRequirement' => 'Number of users reaching the shipping selection, number of users who abandon the cart at this stage.',
        ],
        self::CAR_PMU => [
            'symbol' => 'PMU',
            'name' => 'Payment Methods Used',
            'description' => 'Payment Methods Used',
            'definition' => 'The diversity of payment methods used by customers.',
            'dataRequirement' => "Information on each transaction's payment method.",
        ],
        self::CAR_TTP => [
            'symbol' => 'TTP',
            'name' => 'Time between Cart and Purchase',
            'description' => 'Time between Cart and Purchase',
            'definition' => 'The duration between when items are added to the cart and when the purchase is completed.',
            'dataRequirement' => 'Timestamps for when items are added to the cart and when the checkout is completed.',
        ],
        self::CAR_OSRC => [
            'symbol' => 'OSRC',
            'name' => 'Out of Stock Rate at Checkout',
            'description' => 'Out of Stock Rate at Checkout',
            'definition' => 'The frequency at which items are out of stock by the time a customer reaches checkout.',
            'dataRequirement' => 'Number of checkouts where at least one item is out of stock, total number of checkouts.',
        ],
        self::CAR_CRR => [
            'symbol' => 'CRR',
            'name' => 'Cart Recovery Rate',
            'description' => 'Cart Recovery Rate',
            'definition' => 'The percentage of abandoned carts that are later recovered.',
            'dataRequirement' => 'Number of initially abandoned carts, number of carts subsequently recovered.',
        ],
        self::CAR_FPR => [
            'symbol' => 'FPR',
            'name' => 'Failed Payment Rate',
            'description' => 'Failed Payment Rate',
            'definition' => 'The percentage of transactions where payment fails.',
            'dataRequirement' => 'Total number of attempted transactions, number of failed payment transactions.',
        ],
        self::CAR_MCCB => [
            'symbol' => 'MCCB',
            'name' => 'Most Common Checkout Barrier',
            'description' => 'Most Common Checkout Barrier',
            'definition' => 'The most frequently encountered obstacle during the checkout process.',
            'dataRequirement' => 'Data on reasons for checkout abandonment or customer feedback.',
        ],
        self::CAR_TTV => [
            'symbol' => 'TTV',
            'name' => 'Total Transactions Value',
            'description' => 'Total Transactions Value',
            'definition' => 'The total monetary value of all transactions.',
            'dataRequirement' => 'Transaction amounts.',
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
