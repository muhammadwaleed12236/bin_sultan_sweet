<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\ProductVariant;

echo "Syncing live stock to initial_stock and resetting stock history timestamp...\n";

// 1. Get all live stocks
$stocks = DB::table('stocks')->where('branch_id', 1)->where('warehouse_id', 1)->get();

foreach ($stocks as $stock) {
    if ($stock->variant_id && $stock->variant_id != 0) {
        ProductVariant::where('id', $stock->variant_id)->update(['stock_qty' => $stock->qty]);
    } else {
        Product::where('id', $stock->product_id)->update(['initial_stock' => $stock->qty]);
    }
}

// 2. Create stock_reset_timestamp.txt with current timestamp
$now = date('Y-m-d H:i:s');
Storage::put('stock_reset_timestamp.txt', $now);

echo "Done! Reset timestamp set to: $now\n";
