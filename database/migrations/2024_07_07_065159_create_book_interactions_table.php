<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->enum('interaction_type', ['view', 'purchase']);
            $table->timestamps();

            // Index for faster queries
            $table->index(['user_id', 'book_id', 'interaction_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_interactions');
    }
};