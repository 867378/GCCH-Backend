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
        Schema::rename('cover_letter', 'cover_letters');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('cover_letters', 'cover_letter');
    }
};
