<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * INSTRUKSI:
     * php artisan make:migration add_colors_to_admin_alerts_table --table=admin_alerts
     * lalu salin isi up() dan down() ke file yang dibuat artisan.
     */
    public function up(): void
    {
        Schema::table('admin_alerts', function (Blueprint $table) {
            $table->string('bg_color')->default('#1a1a2e')->after('position');
            $table->string('border_color')->default('#4a5568')->after('bg_color');
            $table->string('text_color')->default('#e2e8f0')->after('border_color');
        });
    }

    public function down(): void
    {
        Schema::table('admin_alerts', function (Blueprint $table) {
            $table->dropColumn(['bg_color', 'border_color', 'text_color']);
        });
    }
};
