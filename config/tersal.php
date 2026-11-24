<?php

return [
    'r2_url' => env('R2_URL'),
    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),
    'wheel_size_api_key' => env('WHEEL_SIZE_API_KEY', ''),
    'admin_email' => env('ADMIN_EMAIL', 'Llantastersal@gmail.com'),

    'company' => [
        'legal_name' => env('COMPANY_LEGAL_NAME', 'TERSAL PERFORMANCE S.A.C.'),
        'ruc' => env('COMPANY_RUC', '20616772296'),
        'address' => env('COMPANY_ADDRESS', 'Jr. Flora Tristán 485, Magdalena del Mar, Lima - Perú'),
        'email' => env('COMPANY_EMAIL', 'administración@llantastersal.com.pe'),
        'phone' => env('COMPANY_PHONE', '+51 902211919'),
        'hours' => 'Lunes a Viernes 09:00 - 18:00 | Sábado 09:00 - 13:00',
    ],
];

