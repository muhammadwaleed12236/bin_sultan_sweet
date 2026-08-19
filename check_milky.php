<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

$p2 = Product::where('item_name', 'like', '%MILKY BREAD%')->first();
if ($p2) {
    echo "Product ID: " . $p2->id . "\n";
    $stocks = DB::table('stocks')->where('product_id', $p2->id)->get();
    echo "Stocks:\n";
    print_r($stocks->toArray());
}
