<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ielts_topics', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('ielts_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('ielts_topics')->cascadeOnDelete();
            $table->string('word');
            $table->text('meaning');
            $table->text('example_sentence');
            $table->timestamps();

            $table->unique(['topic_id', 'word']);
            $table->index('word');
        });

        Schema::create('ielts_collocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('word_id')->constrained('ielts_words')->cascadeOnDelete();
            $table->string('collocation');
            $table->timestamps();
        });

        Schema::create('ielts_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('ielts_topics')->cascadeOnDelete();
            $table->foreignId('word_id')->nullable()->constrained('ielts_words')->nullOnDelete();
            $table->string('quiz_type')->default('mcq');
            $table->text('question');
            $table->json('options_json');
            $table->string('correct_answer');
            $table->timestamps();
        });

        Schema::create('ielts_user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('word_id')->constrained('ielts_words')->cascadeOnDelete();
            $table->enum('status', ['new', 'learning', 'mastered'])->default('new');
            $table->unsignedTinyInteger('correct_streak')->default(0);
            $table->dateTime('last_reviewed_at')->nullable();
            $table->dateTime('next_review_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'word_id']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'next_review_at']);
        });

        Schema::create('ielts_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained('ielts_topics')->cascadeOnDelete();
            $table->unsignedSmallInteger('total_questions');
            $table->unsignedSmallInteger('score');
            $table->json('weak_word_ids')->nullable();
            $table->dateTime('attempted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ielts_quiz_attempts');
        Schema::dropIfExists('ielts_user_progress');
        Schema::dropIfExists('ielts_quizzes');
        Schema::dropIfExists('ielts_collocations');
        Schema::dropIfExists('ielts_words');
        Schema::dropIfExists('ielts_topics');
    }
};
