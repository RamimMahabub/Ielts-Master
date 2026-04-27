<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ----- Question Bank -----
        Schema::create('question_bank_items', function (Blueprint $table) {
            $table->id();
            $table->enum('module', ['listening', 'reading', 'writing', 'speaking']);
            $table->string('title');

            // Listening
            $table->string('audio_path')->nullable();
            $table->longText('transcript')->nullable();

            // Reading
            $table->longText('passage_html')->nullable();
            $table->string('passage_subtitle')->nullable();

            // Writing / Speaking shared prompt
            $table->longText('prompt_html')->nullable();
            $table->string('image_path')->nullable();

            // Module-specific extras (task_number, part_number, cue_card, min_words, etc.)
            $table->json('meta_json')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['module', 'created_at']);
        });

        Schema::create('question_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('question_bank_items')->cascadeOnDelete();
            $table->integer('order_index')->default(0);
            $table->string('question_type');     // mcq_single, tfng, ynng, matching_headings, ...
            $table->text('instructions')->nullable();
            $table->json('shared_data_json')->nullable(); // e.g. heading list, matching options, table layout
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('question_groups')->cascadeOnDelete();
            $table->integer('q_number');                 // 1..40 within mock test module
            $table->integer('order_index')->default(0);
            $table->text('prompt')->nullable();
            $table->json('options_json')->nullable();    // [{key:'A', text:'...'}]
            $table->json('correct_answers_json')->nullable(); // ["true"] or ["A","C"] or ["water","H2O"]
            $table->decimal('points', 5, 2)->default(1);
            $table->timestamps();

            $table->index(['group_id', 'q_number']);
        });

        // ----- Mock Tests -----
        Schema::create('mock_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('test_type', ['academic', 'general'])->default('academic');
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('mock_test_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_test_id')->constrained('mock_tests')->cascadeOnDelete();
            $table->enum('module', ['listening', 'reading', 'writing', 'speaking']);
            $table->integer('order_index')->default(0);
            $table->integer('duration_minutes');
            $table->timestamps();

            $table->unique(['mock_test_id', 'module']);
        });

        Schema::create('mock_test_module_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_test_module_id')->constrained('mock_test_modules')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('question_bank_items')->cascadeOnDelete();
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // ----- Attempts -----
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mock_test_id')->constrained('mock_tests')->cascadeOnDelete();
            $table->enum('status', ['in_progress', 'pending_evaluation', 'completed'])->default('in_progress');

            $table->enum('current_module', ['listening', 'reading', 'writing', 'speaking'])->default('listening');
            $table->timestamp('module_started_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->integer('listening_raw')->nullable();
            $table->integer('reading_raw')->nullable();
            $table->decimal('listening_band', 3, 1)->nullable();
            $table->decimal('reading_band', 3, 1)->nullable();
            $table->decimal('writing_band', 3, 1)->nullable();
            $table->decimal('speaking_band', 3, 1)->nullable();
            $table->decimal('overall_band', 3, 1)->nullable();

            // Speaking recording (uploaded by browser MediaRecorder)
            $table->string('speaking_audio_path')->nullable();

            $table->timestamps();
        });

        Schema::create('test_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('test_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->longText('answer_text')->nullable();      // free-text / essay
            $table->json('answer_json')->nullable();          // multi-select etc.
            $table->boolean('is_correct')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_attempt_answers');
        Schema::dropIfExists('test_attempts');
        Schema::dropIfExists('mock_test_module_items');
        Schema::dropIfExists('mock_test_modules');
        Schema::dropIfExists('mock_tests');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_groups');
        Schema::dropIfExists('question_bank_items');
    }
};
