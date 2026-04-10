<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * INSTRUKSI:
     * php artisan make:migration create_user_background_settings_table
     * lalu salin isi up() dan down() ke file yang dibuat artisan.
     */
    public function up(): void
    {
        Schema::create('user_background_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Mode: 'none' | 'pattern' | 'image'
            $table->string('mode')->default('none');

            // Pattern settings
            $table->string('pattern')->nullable();         // e.g. 'tiles', 'cubes', 'polka', ...
            $table->unsignedSmallInteger('pattern_size')->default(100); // 50–500px

            // Image settings
            $table->string('image_url')->nullable();       // https://...
            $table->string('filter')->default('none');     // 'none' | 'blur' | 'dim'
            $table->unsignedTinyInteger('blur_amount')->default(4);      // 0–20px
            $table->unsignedTinyInteger('transparency')->default(0);     // 0=none 1=subtle 2=transparent 3=max

            // Shared
            $table->string('pattern_color1')->default('#1e293b');
            $table->string('pattern_color2')->default('#334155');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_background_settings');
    }
};
