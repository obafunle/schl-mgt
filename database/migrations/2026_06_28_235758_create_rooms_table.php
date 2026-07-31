<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['classroom', 'laboratory', 'library', 'auditorium', 'office', 'other'])->default('classroom');
            $table->integer('capacity')->default(40);
            $table->string('floor')->nullable();
            $table->string('building')->nullable();
            $table->json('facilities')->nullable(); // ['projector', 'whiteboard', 'air_conditioner']
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};