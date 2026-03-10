<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('content_assets', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->longText('body_text')->nullable();
            $table->string('file_path')->nullable();
            $table->longText('transcript_text')->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamps();
        });

        Schema::create('question_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('content_assets')->onDelete('cascade');
            $table->text('instructions')->nullable();
            $table->string('question_type');
            $table->integer('start_no');
            $table->integer('end_no');
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('question_groups')->onDelete('cascade');
            $table->integer('q_no');
            $table->text('prompt');
            $table->json('meta_json')->nullable();
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->string('label')->nullable();
            $table->text('text');
            $table->timestamps();
        });

        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->text('answer_text');
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        Schema::create('mock_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('duration_minutes')->default(150);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('mock_test_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_test_id')->constrained('mock_tests')->onDelete('cascade');
            $table->string('section_type');
            $table->integer('order_index');
            $table->timestamps();
        });

        Schema::create('mock_test_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_test_section_id')->constrained('mock_test_sections')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('content_assets')->onDelete('cascade');
            $table->integer('order_index');
            $table->timestamps();
        });

        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mock_test_id')->constrained('mock_tests')->onDelete('cascade');
            $table->string('status');
            $table->decimal('raw_score', 8, 2)->nullable();
            $table->string('placeholder_band')->nullable();
            $table->timestamps();
        });

        Schema::create('test_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('test_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_attempt_answers');
        Schema::dropIfExists('test_attempts');
        Schema::dropIfExists('mock_test_section_items');
        Schema::dropIfExists('mock_test_sections');
        Schema::dropIfExists('mock_tests');
        Schema::dropIfExists('question_answers');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_groups');
        Schema::dropIfExists('content_assets');
    }
};
