<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weddings', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('groom_short_name', 80);
            $table->string('bride_short_name', 80);
            $table->string('groom_full_name', 160);
            $table->string('bride_full_name', 160);
            $table->text('parents_names')->nullable();
            $table->date('date');
            $table->time('time_start');
            $table->time('time_end');
            $table->string('location_name', 160);
            $table->text('location_address');
            $table->text('maps_url')->nullable();
            $table->text('waze_url')->nullable();
            $table->json('family_contacts')->nullable();
            $table->text('bg_music_url')->nullable();
            $table->string('bg_music_path')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->json('decoration_settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weddings');
    }
};
