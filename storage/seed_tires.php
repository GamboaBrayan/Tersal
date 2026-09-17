<?php
use App\Models\Tire;

$widths = [195, 205, 215, 225, 235];
$profiles = [55, 60, 65, 70];
$rims = [15, 16, 17, 18];
$models = ["SportMaxx", "EcoPlus", "AllTerrain", "HighwayStar", "WinterPro"];

for ($i = 2; $i <= 16; $i++) {
    $price = rand(250, 600);
    $hasOffer = rand(0, 1);
    
    Tire::create([
        "product_code" => "TT" . str_pad($i, 6, "0", STR_PAD_LEFT),
        "brand_id" => 1,
        "category_id" => 1,
        "model" => $models[array_rand($models)] . " v" . rand(1,5),
        "width" => $widths[array_rand($widths)],
        "profile" => $profiles[array_rand($profiles)],
        "rim" => $rims[array_rand($rims)],
        "load_index" => (string)rand(85, 110),
        "speed_rating" => ["H", "V", "W", "T"][rand(0, 3)],
        "price" => $price . ".00",
        "offer_price" => $hasOffer ? ($price - rand(20, 50)) . ".00" : null,
        "currency" => "PEN",
        "stock" => rand(5, 50),
        "status" => true,
        "is_promoted" => $hasOffer ? 1 : 0,
        "year" => "202" . rand(3,6),
        "version" => "Estándar"
    ]);
}
echo "15 tires created.\n";

