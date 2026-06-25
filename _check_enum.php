<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$enums = ['payment_method', 'payment_status', 'delivery_status', 'order_status', 'seller_status'];
foreach ($enums as $enum) {
    try {
        $results = Illuminate\Support\Facades\DB::select("SELECT unnest(enum_range(NULL::$enum))::text AS value");
        $vals = array_map(fn($r) => $r->value, $results);
        echo "$enum: " . implode(', ', $vals) . "\n";
    } catch (\Exception $e) {
        echo "$enum: NOT FOUND\n";
    }
}
