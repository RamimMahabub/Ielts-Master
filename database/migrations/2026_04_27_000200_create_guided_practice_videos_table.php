<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guided_practice_videos', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['listening', 'reading', 'writing', 'speaking']);
            $table->string('title');
            $table->string('video_path');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['category', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guided_practice_videos');
    }
};
