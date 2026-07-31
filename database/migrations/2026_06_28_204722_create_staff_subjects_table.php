<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('role', ['primary', 'secondary', 'assistant'])->default('primary');
            $table->integer('weekly_hours')->default(4);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->unique(['staff_id', 'subject_id', 'class_id'], 'unique_staff_subject_class');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_subjects');
    }
};