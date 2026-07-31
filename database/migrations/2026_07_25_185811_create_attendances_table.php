<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('marked_by')->constrained('users');
            $table->timestamps();

            $table->unique(['student_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
