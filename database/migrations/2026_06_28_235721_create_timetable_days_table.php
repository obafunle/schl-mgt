<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('timetable_days', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Monday, Tuesday, etc.
            $table->string('short_name')->unique(); // Mon, Tue, etc.
            $table->integer('day_number')->unique(); // 1, 2, 3, 4, 5, 6, 7
            $table->boolean('is_school_day')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('timetable_days');
    }
};