<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductVariant;
$v = ProductVariant::find(93);
echo "Variant 93 stock_qty: " . $v->stock_qty . "\n";
