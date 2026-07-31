<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Mathematics, English, Physics
            $table->string('code')->unique(); // MAT, ENG, PHY
            $table->string('short_name')->nullable(); // Maths, Eng, Phy
            $table->enum('category', ['core', 'science', 'arts', 'vocational', 'other']);
            $table->enum('level', ['primary', 'junior', 'senior']);
            $table->integer('weekly_hours')->default(4);
            $table->integer('exam_weight')->default(60); // 60% of total
            $table->integer('ca_weight')->default(40); // 40% of total
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subjects');
    }
};