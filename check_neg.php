<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$stocks = DB::table('stocks')->where('qty', '<', 0)->count();
echo "Negative stocks count: $stocks\n";

$pos = DB::table('stocks')->where('qty', '>=', 0)->count();
echo "Positive/Zero stocks count: $pos\n";
