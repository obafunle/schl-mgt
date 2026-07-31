<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
            Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_arm_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade'); // ← THIS MUST EXIST
            $table->foreignId('teacher_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->integer('weekly_hours')->default(4);
            $table->boolean('is_core')->default(false);
            $table->boolean('is_compulsory')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['class_id', 'subject_id', 'class_arm_id'], 'unique_class_subject_arm');
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_subjects');
    }
};
