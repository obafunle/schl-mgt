<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            
            // Staff Details
            $table->enum('staff_type', ['teacher', 'admin', 'support', 'accountant', 'librarian', 'other'])->default('teacher');
            $table->enum('employment_type', ['permanent', 'contract', 'part-time', 'intern'])->default('permanent');
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            
            // Qualifications
            $table->json('qualifications')->nullable(); // [{degree: "BSc", institution: "UNILAG", year: 2020}]
            $table->json('experience')->nullable(); // [{position: "Teacher", school: "ABC School", years: 2}]
            
            // Salary
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('allowances', 15, 2)->default(0);
            $table->decimal('deductions', 15, 2)->default(0);
            
            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive', 'suspended', 'terminated'])->default('active');
            
            // Meta
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('staff_id');
            $table->index('email');
            $table->index('staff_type');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff');
    }
};