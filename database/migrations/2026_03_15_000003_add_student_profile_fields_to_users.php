<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('name');
            $table->string('country')->nullable()->after('phone');
            $table->string('timezone')->default('UTC')->after('country');
            $table->string('exam_type')->nullable()->after('target_band');
            $table->text('study_goal')->nullable()->after('exam_type');
            $table->unsignedInteger('daily_study_minutes')->nullable()->after('study_goal');
            $table->text('bio')->nullable()->after('daily_study_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'country',
                'timezone',
                'exam_type',
                'study_goal',
                'daily_study_minutes',
                'bio',
            ]);
        });
    }
};
