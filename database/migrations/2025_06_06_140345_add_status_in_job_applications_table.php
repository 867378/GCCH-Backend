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
        Schema::table('job_applications', function (Blueprint $table) {
            $table->enum('status',[
                'applied',
                'for_interview',
                'screening',
                'interviewed',
                'rejected',
                'accepted',
                'hired',
            ])->default('applied')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            // Change the 'status' column to an ENUM type with the specified values
            $table->enum('status', [
                'applied',
                'screening',
                'for_interview',
                'accepted',
                'rejected',
                'hired',
            ])->default('applied')->change();
        });
    }
};
