<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// INSTRUKSI EKSEKUSI:
// 1. Ganti nama file ini dengan timestamp saat ini, contoh:
//    2024_12_01_000001_add_hide_from_admin_to_servers_table.php
//    ATAU jalankan: php artisan make:migration add_hide_from_admin_to_servers_table --table=servers
//    lalu salin isi file ini ke dalam file yang dibuat artisan.
// 2. Letakkan di: database/migrations/
// 3. Jalankan: php artisan migrate

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->boolean('hide_from_admin')->default(false)->after('backup_limit');
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('hide_from_admin');
        });
    }
};
