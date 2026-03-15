<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('test_attempts', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('placeholder_band');
            }

            if (!Schema::hasColumn('test_attempts', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('test_attempts', 'completed_at')) {
                $table->dropColumn('completed_at');
            }

            if (Schema::hasColumn('test_attempts', 'started_at')) {
                $table->dropColumn('started_at');
            }
        });
    }
};
