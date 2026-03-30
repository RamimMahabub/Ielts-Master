<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->unsignedTinyInteger('section_no');
            $table->unsignedSmallInteger('section_order');
            $table->unsignedInteger('global_order')->index();
            $table->timestamps();
        });

        Schema::create('user_vocabulary_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vocabulary_word_id')->constrained('vocabulary_words')->cascadeOnDelete();
            $table->boolean('mastered')->default(false);
            $table->unsignedInteger('times_correct')->default(0);
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'vocabulary_word_id']);
        });

        Schema::create('vocabulary_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('section_no');
            $table->unsignedSmallInteger('total_questions');
            $table->unsignedSmallInteger('score');
            $table->timestamp('attempted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_quiz_attempts');
        Schema::dropIfExists('user_vocabulary_progress');
        Schema::dropIfExists('vocabulary_words');
    }
};
