<?php

return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'cashier-model' => env('CASHIER_MODEL'),
    'logger' => env('CASHIER_LOGGER')
];
