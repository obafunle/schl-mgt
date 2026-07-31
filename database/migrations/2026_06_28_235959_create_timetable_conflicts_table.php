<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('timetable_conflicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->nullable()->constrained('timetable_entries')->onDelete('cascade');
            $table->foreignId('conflicting_entry_id')->nullable()->constrained('timetable_entries')->onDelete('cascade');
            $table->enum('conflict_type', ['teacher', 'room', 'class', 'time']);
            $table->text('description');
            $table->boolean('is_resolved')->default(false);
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('timetable_conflicts');
    }
};