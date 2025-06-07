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
            $table->string('street_address')->after('expertise');
            $table->string('city')->after('street_address');
            $table->string('province')->after('city');
            $table->string('country')->after('province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('street_address');
            $table->dropColumn('city');
            $table->dropColumn('province');
            $table->dropColumn('country');
        });
    }
};
