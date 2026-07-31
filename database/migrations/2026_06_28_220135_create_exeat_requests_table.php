<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exeat_requests', function (Blueprint $table) {
            $table->id();
            $table->string('exeat_number')->unique();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->constrained()->onDelete('cascade');
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            
            // Exeat details
            $table->date('departure_date');
            $table->date('return_date');
            $table->time('departure_time')->nullable();
            $table->time('return_time')->nullable();
            $table->text('reason');
            $table->text('destination')->nullable();
            $table->text('accompanied_by')->nullable();
            $table->text('contact_during_absence')->nullable();
            
            // Approvals
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'completed'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('departure_confirmed_at')->nullable();
            $table->timestamp('return_confirmed_at')->nullable();
            
            // Meta
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['student_id', 'status']);
            $table->index(['departure_date', 'return_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exeat_requests');
    }
};