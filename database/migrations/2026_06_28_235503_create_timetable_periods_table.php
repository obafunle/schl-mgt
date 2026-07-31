<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('timetable_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Period 1, Period 2, Break, etc.
            $table->string('code')->unique(); // P1, P2, BREAK
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->enum('type', ['academic', 'break', 'assembly', 'sports', 'other'])->default('academic');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('timetable_periods');
    }
};