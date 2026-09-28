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
        Schema::create('api_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('method');
            $table->text('url');
            $table->json('query_parameters')->nullable();
            $table->json('headers')->nullable();
            $table->string('body_type')->default('none');
            $table->text('body')->nullable();
            $table->boolean('is_saved')->default(false);
            
            // Last response
            $table->integer('last_status')->nullable();
            $table->integer('last_duration_ms')->nullable();
            $table->json('last_response_headers')->nullable();
            $table->text('last_response_body')->nullable();
            $table->string('last_error')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_requests');
    }
};
