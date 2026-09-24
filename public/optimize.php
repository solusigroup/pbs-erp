<?php
/**
 * PBS-ERP Production Optimization Utility
 * Akses via browser: https://pbs.simpleakunting.biz.id/optimize.php
 * HAPUS FILE INI SETELAH DIJALANKAN DEMI KEAMANAN!
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h2>PBS-ERP Production Optimizer</h2>";

try {
    $kernel->call('config:cache');
    echo "<p style='color:green;'>✅ Config cached successfully</p>";
} catch (\Exception $e) {
    echo "<p style='color:red;'>❌ Config cache failed: " . $e->getMessage() . "</p>";
}

try {
    $kernel->call('route:cache');
    echo "<p style='color:green;'>✅ Routes cached successfully</p>";
} catch (\Exception $e) {
    echo "<p style='color:red;'>❌ Route cache failed: " . $e->getMessage() . "</p>";
}

try {
    $kernel->call('view:cache');
    echo "<p style='color:green;'>✅ Views cached successfully</p>";
} catch (\Exception $e) {
    echo "<p style='color:red;'>❌ View cache failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p style='font-weight:bold; color:orange;'>⚠ PENTING: Hapus file optimize.php ini sekarang demi keamanan!</p>";
echo "<p><a href='/'>&larr; Kembali ke PBS-ERP</a></p>";
