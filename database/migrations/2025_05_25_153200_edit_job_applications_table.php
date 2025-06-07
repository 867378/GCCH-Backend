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
        // In the migration file
        Schema::table('job_applications', function (Blueprint $table) {
            $table->enum('offer_status', ['none', 'offered', 'accepted', 'rejected'])->default('none');
            $table->boolean('finalized')->default(false); // true once applicant accepts a job offer
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // In the migration file
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn('offer_status');
            $table->dropColumn('finalized'); // true once applicant accepts a job offer
        });

    }
};
