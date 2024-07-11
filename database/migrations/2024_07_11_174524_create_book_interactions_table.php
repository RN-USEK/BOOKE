<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create the book_interactions table
        Schema::create('book_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->string('interaction_type');
            $table->integer('score');
            $table->timestamps();

            $table->unique(['user_id', 'book_id', 'interaction_type']);
        });

        // Add the index for faster queries
        Schema::table('book_interactions', function (Blueprint $table) {
            $table->index(['user_id', 'book_id', 'interaction_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_interactions');
    }
};