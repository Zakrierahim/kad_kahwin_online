<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedding_id')->constrained()->cascadeOnDelete();
            $table->string('guest_name', 100);
            $table->string('phone_number', 20);
            $table->enum('status', ['hadir', 'tidak_hadir']);
            $table->unsignedInteger('total_pax')->default(0);
            $table->timestamps();

            $table->index(['wedding_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvps');
    }
};
