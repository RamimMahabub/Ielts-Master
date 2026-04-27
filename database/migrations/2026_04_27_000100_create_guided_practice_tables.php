<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('learning_resources', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['listening', 'reading', 'writing', 'speaking']);
            $table->longText('content');
            $table->integer('order_index')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['category', 'order_index']);
        });

        Schema::create('class_recordings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('recording_path');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['created_at']);
        });

        Schema::create('student_learning_resource_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('learning_resource_id')->constrained('learning_resources')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'learning_resource_id']);
        });

        Schema::create('student_class_recording_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_recording_id')->constrained('class_recordings')->cascadeOnDelete();
            $table->enum('status', ['watch_later', 'completed'])->default('watch_later');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'class_recording_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_class_recording_statuses');
        Schema::dropIfExists('student_learning_resource_statuses');
        Schema::dropIfExists('class_recordings');
        Schema::dropIfExists('learning_resources');
    }
};
