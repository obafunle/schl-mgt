<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('parent_children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('relationship', ['father', 'mother', 'guardian', 'other'])->default('guardian');
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('can_receive_notifications')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
            
            $table->unique(['parent_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('parent_children');
    }
};