<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained();
            $table->foreignId('class_arm_id')->constrained();
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('term_id')->constrained();
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'completed', 'transferred', 'dropped'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->unique(['student_id', 'academic_year_id', 'term_id'], 'unique_enrollment');
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
};