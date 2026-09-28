<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->index();
            $table->text('description');
            $table->text('error_message')->nullable();
            $table->string('error_type')->nullable();
            $table->string('environment')->nullable();
            $table->string('severity')->index();
            $table->string('status')->index();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamp('solved_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};
