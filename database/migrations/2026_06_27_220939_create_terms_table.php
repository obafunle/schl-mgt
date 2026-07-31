<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('name'); // First Term, Second Term, Third Term
            $table->integer('term_number'); // 1, 2, 3
            $table->date('start_date');
            $table->date('end_date');
            $table->date('exam_start_date')->nullable();
            $table->date('exam_end_date')->nullable();
            $table->date('result_publication_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('terms');
    }
};