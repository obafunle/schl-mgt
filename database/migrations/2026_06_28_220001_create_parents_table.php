<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('occupation')->nullable();
            $table->string('photo')->nullable();
            
            // Relationships
            $table->json('children_ids')->nullable(); // Array of student IDs
            
            // Preferences
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->string('preferred_language')->default('en');
            
            // Verification
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_code')->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            
            // Meta
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('email');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('parents');
    }
};