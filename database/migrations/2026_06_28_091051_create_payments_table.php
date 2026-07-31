<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('reference')->unique();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            
            // Payment details
            $table->decimal('amount', 15, 2);
            $table->decimal('fee_charged', 15, 2)->nullable();
            $table->string('payment_method'); // card, bank_transfer, cash, paystack
            $table->string('gateway')->default('paystack');
            
            // Paystack specific
            $table->string('paystack_reference')->nullable();
            $table->string('paystack_authorization_code')->nullable();
            $table->json('paystack_response')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'success', 'failed', 'reversed'])->default('pending');
            
            // Dates
            $table->timestamp('payment_date');
            $table->timestamp('verified_at')->nullable();
            
            // Meta
            $table->json('meta')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['student_id', 'payment_date']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};