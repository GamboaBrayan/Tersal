<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = App\Http\Controllers\VehicleSearchController::getTiresForVehicle('acura', 'cdx', '2021');
print_r($data);
