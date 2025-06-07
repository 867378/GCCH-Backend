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
        DB::table('job_applications')
            ->where('status', 'pending')
            ->update(['status' => 'applied']);

        Schema::table('job_applications', function (Blueprint $table) {
            $table->enum('status', ['applied', 'interview', 'assessment', 'rejected', 'accepted'])
                  ->default('applied')
                  ->change();
            $table->dateTime('scheduled_at')->nullable()->after('status');
            $table->text('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->string('status')->change();
        $table->dropColumn('scheduled_at');
        $table->dropColumn('comment');
    }
};
