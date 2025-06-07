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
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('type', ['application_update','message','job_application','inquiry','general'])
                  ->change();
        });

        Schema::table('applicants', function (Blueprint $table) {
            $table->enum('course', ['BSIT', 'BSCS', 'BSEMC', 'BSN', 'BSM', 'BSA', 'BSBA-FM', 'BSBA-HRM', 'BSBA-MM', 'BSCA', 'BSHM', 'BSTM', 'BAComm', 'BECEd', 'BCAEd', 'BPEd', 'BEED', 'BSEd-Eng', 'BSEd-Math', 'BSEd-Fil', 'BSEd-SS', 'BSEd-Sci'])->after('phone_number')
                  ->change();
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->enum('recommended_course', ['BSIT', 'BSCS', 'BSEMC', 'BSN', 'BSM', 'BSA', 'BSBA-FM', 'BSBA-HRM', 'BSBA-MM', 'BSCA', 'BSHM', 'BSTM', 'BAComm', 'BECEd', 'BCAEd', 'BPEd', 'BEED', 'BSEd-Eng', 'BSEd-Math', 'BSEd-Fil', 'BSEd-SS', 'BSEd-Sci'])
                  ->after('monthly_salary')
                  ->change();
            $table->enum('recommended_course_2',['BSIT','BSCS','BSEMC','BSN','BSM','BSA','BSBA-FM','BSBA-HRM','BSBA-MM','BSCA','BSHM','BSTM','BAComm','BECEd','BCAEd','BPEd','BEED','BSEd-Eng','BSEd-Math','BSEd-Fil','BSEd-SS','BSEd-Sci'])
                  ->after('recommended_course')
                  ->nullable()
                  ->change();
            $table->enum('recommended_course_3',['BSIT','BSCS','BSEMC','BSN','BSM','BSA','BSBA-FM','BSBA-HRM','BSBA-MM','BSCA','BSHM','BSTM','BAComm','BECEd','BCAEd','BPEd','BEED','BSEd-Eng','BSEd-Math','BSEd-Fil','BSEd-SS','BSEd-Sci'])
                  ->after('recommended_course_2')
                  ->nullable()
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('type', ['application_update','message','job_application','inquiry'])
                  ->change();
        });
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('course')->change();
        });
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('recommended_course')->change();
            $table->string('recommended_course_2')->change();
            $table->string('recommended_course_3')->change();
        });
    }
};
