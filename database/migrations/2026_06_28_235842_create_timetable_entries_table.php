<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_arm_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('day_id')->constrained('timetable_days');
            $table->foreignId('period_id')->constrained('timetable_periods');
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            
            // Additional details
            $table->enum('status', ['scheduled', 'rescheduled', 'cancelled', 'replacement'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            
            // Recurrence
            $table->boolean('is_recurring')->default(true);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            
            // Audit
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['class_id', 'day_id', 'period_id']);
            $table->index(['teacher_id', 'day_id', 'period_id']);
            $table->index(['room_id', 'day_id', 'period_id']);
            $table->unique(['class_id', 'day_id', 'period_id', 'term_id', 'academic_year_id'], 'unique_timetable_class_period');
        });
    }

    public function down()
    {
        Schema::dropIfExists('timetable_entries');
    }
};