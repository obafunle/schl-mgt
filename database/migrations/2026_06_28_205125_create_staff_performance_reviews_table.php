<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users');
            $table->date('review_date');
            $table->string('review_period'); // Q1 2024
            
            // Ratings (1-5)
            $table->integer('punctuality')->nullable();
            $table->integer('productivity')->nullable();
            $table->integer('teamwork')->nullable();
            $table->integer('communication')->nullable();
            $table->integer('technical_skills')->nullable();
            $table->integer('leadership')->nullable();
            $table->integer('problem_solving')->nullable();
            $table->float('overall_rating', 3, 2)->nullable();
            
            // Comments
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('goals')->nullable();
            $table->text('reviewer_comments')->nullable();
            $table->text('staff_comments')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['staff_id', 'review_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_performance_reviews');
    }
};