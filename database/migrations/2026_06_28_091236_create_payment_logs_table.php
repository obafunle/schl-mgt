<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('event')->nullable();
            $table->json('payload')->nullable();
            $table->text('response')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
            
            $table->index('reference');
            $table->index('event');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_logs');
    }
};