<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_arm_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            
            // Exam details
            $table->string('name')->nullable(); // First Term Examination, etc.
            $table->date('exam_date');
            $table->date('ca_deadline')->nullable();
            $table->integer('total_marks')->default(100);
            $table->integer('ca_weight')->default(40); // 40% CA
            $table->integer('exam_weight')->default(60); // 60% Exam
            
            // Status
            $table->enum('status', ['draft', 'published', 'completed', 'archived'])->default('draft');
            
            // Meta
            $table->json('settings')->nullable(); // Additional settings
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['class_id', 'class_arm_id', 'subject_id', 'term_id', 'academic_year_id'], 'unique_examination');
        });
    }

    public function down()
    {
        Schema::dropIfExists('examinations');
    }
};