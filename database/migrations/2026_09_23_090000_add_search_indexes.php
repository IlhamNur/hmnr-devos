<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Projects search indexes
        Schema::table('projects', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'updated_at']);
        });

        // Problems search indexes
        Schema::table('problems', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'severity']);
            $table->index(['user_id', 'project_id']);
            $table->index(['user_id', 'updated_at']);
        });

        // Solutions search indexes
        Schema::table('solutions', function (Blueprint $table) {
            $table->index(['user_id', 'problem_id']);
        });

        // Snippets search indexes
        Schema::table('snippets', function (Blueprint $table) {
            $table->index(['user_id', 'language']);
            $table->index(['user_id', 'is_favorite']);
            $table->index(['user_id', 'updated_at']);
        });

        // Knowledge items search indexes
        Schema::table('knowledge_items', function (Blueprint $table) {
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'updated_at']);
        });

        Schema::table('problems', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'severity']);
            $table->dropIndex(['user_id', 'project_id']);
            $table->dropIndex(['user_id', 'updated_at']);
        });

        Schema::table('solutions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'problem_id']);
        });

        Schema::table('snippets', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'language']);
            $table->dropIndex(['user_id', 'is_favorite']);
            $table->dropIndex(['user_id', 'updated_at']);
        });

        Schema::table('knowledge_items', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'type']);
            $table->dropIndex(['user_id', 'updated_at']);
        });
    }
};

