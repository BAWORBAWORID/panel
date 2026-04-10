<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admin_alerts', function (Blueprint $table) {
            // Icon: salah satu dari 8 PNG yang tersedia
            $table->string('icon')->default('megaphone')->after('type');
            // Position: sticky (ikut scroll) atau static (tetap di atas)
            $table->string('position')->default('sticky')->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('admin_alerts', function (Blueprint $table) {
            $table->dropColumn(['icon', 'position']);
        });
    }
};
