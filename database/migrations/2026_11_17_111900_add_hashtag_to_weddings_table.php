<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->string('hashtag', 40)->nullable()->after('parents_names');
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn('hashtag');
        });
    }
};
