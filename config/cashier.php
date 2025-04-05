<?php

return [
    'prices' => [
        'test_monthly' => 'pri_01jqfkd6pkj6r1yw6w7ttvwhmh',
        'test_yearly' => 'pri_01jqs4apgyh5t973h6k3a8pcg5',
        'basic_yearly' => 'pri_01jqgkzhz0p9bjpk2cgrf76sjp',
        'premium_monthly' => 'pri_01jqs4x01ntg2gqpj0pgjfenr6',
        'premium_yearly' => 'pri_01jqs4xmv02mnbe44kskea47rw',
        'pro_monthly' => 'pri_01jqs52dnh230actvf1j9w14pq',
        'pro_yearly' => 'pri_01jqs51jcdkn00j4s4swe9g3fv',
        'team_standard_yearly' => 'pri_01jqs57q3hmrm425cv464c91aj',
        'team_standard_monthly' => 'pri_01jqs56dq80cvcc6ywykfyyy75',
        'team_growth_monthly' => 'pri_01jqs5d5y4jqpdqce5fzksjtjp',
        'team_growth_yearly' => 'pri_01jqs5cbcnc8kpcc4bhvhzcc21',
        'team_elite_monthly' => 'pri_01jqfkd6pkj6r1yw6w7ttvwh3h',
        'team_elite_yearly' => 'pri_01jqfkd6pkj6r1yw6w7ttvwh4h',
    ],
    'plans' => [
        'test' => [
            'product_id' => 'pro_01jqfkc03ebg9t43r4nr9kf10y',
            'coin' => 100,
            'prices' => [
                'monthly' => 'pri_01jqfkd6pkj6r1yw6w7ttvwhmh',
                'yearly' => 'pri_01jqs4apgyh5t973h6k3a8pcg5',
            ],
        ],
        'basic' => [
            'product_id' => 'pro_01jqgktf1wdbzn1b3fa2qc4t9q',
            'coin' => 10000,
            'prices' => [
                'yearly' => 'pri_01jqgkzhz0p9bjpk2cgrf76sjp',
            ],
        ],
        'premium' => [
            'product_id' => 'pro_01jqs4pcfyqwx8fndxvbv987x5',
            'coin' => 30000,
            'prices' => [
                'monthly' => 'pri_01jqs4x01ntg2gqpj0pgjfenr6',
                'yearly' => 'pri_01jqs4xmv02mnbe44kskea47rw',
            ],
        ],
        'pro' => [
            'product_id' => 'pro_01jqs4q779y5r1xdv4p2hfg7ce',
            'coin' => 200000,
            'prices' => [
                'monthly' => 'pri_01jqs52dnh230actvf1j9w14pq',
                'yearly' => 'pri_01jqs51jcdkn00j4s4swe9g3fv',
            ],
        ],
        'team_standard' => [
            'product_id' => 'xx',
            'coin' => 450000,
            'interval' => 'weekly',
            'prices' => [
                'monthly' => 'pri_01jqs56dq80cvcc6ywykfyyy75',
                'yearly' => 'pri_01jqs57q3hmrm425cv464c91aj',
            ],
        ],
        'team_growth' => [
            'product_id' => 'xx',
            'coin' => 1000000,
            'interval' => 'weekly',
            'prices' => [
                'monthly' => 'pri_01jqs5d5y4jqpdqce5fzksjtjp',
                'yearly' => 'pri_01jqs5cbcnc8kpcc4bhvhzcc21',
            ],
        ],
        'team_elite' => [
            'product_id' => 'xx',
            'coin' => 200000,
            'interval' => 'weekly',
            'prices' => [
                'monthly' => 'pri_01jqfkd6pkj6r1yw6w7ttvwh3h',
                'yearly' => 'pri_01jqfkd6pkj6r1yw6w7ttvwh4h',
            ],
        ],
    ],
    'paddle' => [
        'public_key' => env('PADDLE_PUBLIC_KEY'),
    ],
];
