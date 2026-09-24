<?php
/**
 * PBS-ERP Production Storage Symlink Utility
 * Akses file ini via browser: https://pbs.simpleakunting.biz.id/storage_link.php
 * Setelah berhasil membuat symlink, file ini dapat dihapus untuk keamanan.
 */

$target = dirname(__DIR__) . '/storage/app/public';
$link = __DIR__ . '/storage';

echo "<h2>PBS-ERP Storage Symlink Utility</h2>";
echo "<p><strong>Target Folder:</strong> <code>{$target}</code></p>";
echo "<p><strong>Link Path:</strong> <code>{$link}</code></p>";

if (!file_exists($target)) {
    mkdir($target, 0755, true);
    echo "<p style='color:orange;'>Folder storage/app/public baru saja dibuat.</p>";
}

if (file_exists($link)) {
    if (is_link($link)) {
        echo "<p style='color:green; font-weight:bold;'>✓ Symlink 'public/storage' sudah ada dan terhubung dengan baik!</p>";
    } else {
        echo "<p style='color:red; font-weight:bold;'>⚠ 'public/storage' sudah ada namun berupa direktori biasa (bukan symlink).</p>";
    }
} else {
    if (function_exists('symlink') && @symlink($target, $link)) {
        echo "<p style='color:green; font-weight:bold;'>✓ Berhasil! Symlink 'public/storage' sukses dibuat.</p>";
    } else {
        echo "<p style='color:red; font-weight:bold;'>Gagal membuat symlink otomatis. Anda dapat mencoba via SSH / cPanel Terminal: <code>php artisan storage:link</code></p>";
    }
}

echo "<hr><p><a href='/'>&larr; Kembali ke Dashboard PBS-ERP</a></p>";
