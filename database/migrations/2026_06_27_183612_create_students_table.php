<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('admission_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            
            // Academic - Make these nullable initially
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('class_arm')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            
            // Parent/Guardian
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->string('parent_email')->nullable();
            
            // Status
            $table->enum('status', ['active', 'graduated', 'transferred', 'suspended'])->default('active');
            
            // Meta
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};