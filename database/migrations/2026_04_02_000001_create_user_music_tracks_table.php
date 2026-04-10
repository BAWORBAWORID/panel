<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_music_tracks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // 'global' = dari spotify2026.mp3 bawaan, 'youtube' = download dari YT, 'upload' = file user
            $table->string('source')->default('global'); // 'global' | 'youtube' | 'upload'

            $table->string('title')->default('Unknown Track');
            $table->string('artist')->nullable();
            $table->string('video_id')->nullable();       // YouTube video ID (jika source=youtube)
            $table->string('file_path')->nullable();      // path relatif dari storage/app/public/music/{user_id}/
            $table->string('download_url')->nullable();   // URL download mp3 dari external API
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['user_id', 'sort_order']);
        });

        // Tabel untuk menyimpan preferensi player per user
        Schema::create('user_music_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedTinyInteger('volume')->default(70);   // 0–100
            $table->boolean('shuffle')->default(false);
            $table->boolean('repeat')->default(false);
            $table->unsignedBigInteger('current_track_id')->nullable(); // last played track id
            $table->foreign('current_track_id')->references('id')->on('user_music_tracks')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_music_preferences');
        Schema::dropIfExists('user_music_tracks');
    }
};
