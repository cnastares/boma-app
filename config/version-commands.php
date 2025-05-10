<?php

return [
    '1.6' => [
        'translate:existing-data',
    ],
    '1.7' => [
        'update:price-type-label',
        'queue:table',
        'migrate',
        'db:seed --class=PlanSeeder',
        'db:seed --class=BannerSeeder',
        'db:seed --class=PostCategorySeeder',
    ],
    '2.0'=>[
        'admin:permissions',
        'ad-type:install',
        'convert:descriptions'
    ]
];
