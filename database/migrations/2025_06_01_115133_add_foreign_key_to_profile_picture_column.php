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
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('profile_picture')->nullable()->change(); // ensure nullable
            $table->foreign('profile_picture')
                ->references('id')
                ->on('profile_pictures')
                ->onDelete('set null');
        });

        Schema::table('applicants', function (Blueprint $table) {
            $table->unsignedBigInteger('profile_picture')->nullable()->change(); // ensure nullable
            $table->foreign('profile_picture')
                ->references('id')
                ->on('profile_pictures')
                ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['profile_picture']);
        });

        Schema::table('applicants', function (Blueprint $table) {
            $table->dropForeign(['profile_picture']);
        });
    }
};
