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
    // Fix invalid data first
    DB::table('companies')->where('profile_picture', '')->update(['profile_picture' => null]);
    DB::table('applicants')->where('profile_picture', '')->update(['profile_picture' => null]);

    Schema::table('companies', function (Blueprint $table) {
        $table->unsignedBigInteger('profile_picture')->nullable()->change();
        $table->foreign('profile_picture')
            ->references('id')
            ->on('profile_pictures')
            ->onDelete('set null');
    });

    Schema::table('applicants', function (Blueprint $table) {
        $table->unsignedBigInteger('profile_picture')->nullable()->change();
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
        Schema::dropIfExists('profile_pictures');
    }
    
};
