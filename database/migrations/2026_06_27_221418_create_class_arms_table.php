<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('class_arms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->string('name'); // A, B, C, Gold, Silver
            $table->string('code')->unique(); // JSS1A, SSS1B
            $table->integer('capacity')->default(40);
            $table->integer('current_enrollment')->default(0);
            $table->foreignId('class_teacher_id')->nullable()->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_arms');
    }
};