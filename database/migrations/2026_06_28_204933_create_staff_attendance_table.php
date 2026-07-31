<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->decimal('hours_worked', 5, 2)->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'half-day', 'holiday', 'leave'])->default('present');
            $table->text('notes')->nullable();
            $table->foreignId('marked_by')->constrained('users');
            $table->timestamps();
            
            $table->unique(['staff_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_attendance');
    }
};