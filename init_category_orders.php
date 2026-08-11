<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;

$categories = Category::orderBy('id')->get();
foreach ($categories as $index => $category) {
    $category->update(['order' => $index + 1]);
}
echo "Categorías ordenadas correctamente.\n";
