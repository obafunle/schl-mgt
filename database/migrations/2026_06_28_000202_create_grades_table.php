<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('examination_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_arm_id')->constrained()->onDelete('cascade');
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            
            // Scores
            $table->decimal('ca_score', 5, 2)->nullable(); // Continuous Assessment
            $table->decimal('exam_score', 5, 2)->nullable();
            $table->decimal('total_score', 5, 2)->nullable();
            $table->decimal('ca_weighted', 5, 2)->nullable(); // CA * weight %
            $table->decimal('exam_weighted', 5, 2)->nullable(); // Exam * weight %
            
            // Nigerian Grading System
            $table->enum('grade', ['A', 'B', 'C', 'D', 'E', 'F'])->nullable();
            $table->string('remark')->nullable(); // Excellent, Good, Fair, Pass, Poor, Fail
            
            // Position in class
            $table->integer('position')->nullable();
            $table->integer('total_students')->nullable();
            
            // Teacher/Admin
            $table->foreignId('entered_by')->constrained('users');
            $table->timestamp('entered_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            // Meta
            $table->json('meta')->nullable();
            
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['student_id', 'examination_id'], 'unique_grade_student_exam');
            
            // Indexes for performance
            $table->index(['class_id', 'term_id', 'academic_year_id']);
            $table->index(['student_id', 'term_id', 'academic_year_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('grades');
    }
};