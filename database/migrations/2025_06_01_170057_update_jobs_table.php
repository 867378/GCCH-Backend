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
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('recommended_expertise');
            $table->string('recommended_expertise_2')->nullable();
            $table->string('recommended_expertise_3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('recommended_expertise');
            $table->dropColumn('recommended_expertise_2');
            $table->dropColumn('recommended_expertise_3');
        });
    }
};
