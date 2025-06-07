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
            $table->string('course')->after('phone_number');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->string('recommended_course')->after('monthly_salary');
            $table->string('recommended_course_2')->after('recommended_course')->nullable();
            $table->string('recommended_course_3')->after('recommended_course_2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('course');
        });
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('recommended_course');
            $table->dropColumn('recommended_course_2');
            $table->dropColumn('recommended_course_3');
        });
    }
};
