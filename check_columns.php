<?php

use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = Schema::getColumnListing('users');
echo "Columns in users table:\n";
foreach ($columns as $col) {
    echo "- $col\n";
}
