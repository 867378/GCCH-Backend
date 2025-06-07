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
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('expertise');
            $table->string('profile_picture')->after('expertise');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('profile_picture');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('expertise');
            $table->dropColumn('profile_picture');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('profile_picture');
        });
    }
};
