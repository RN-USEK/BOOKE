<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('new_book_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->string('interaction_type');
            $table->integer('score');
            $table->timestamps();

            $table->unique(['user_id', 'book_id', 'interaction_type']);
        });

        // Migrate data from the old table to the new one
        DB::statement("INSERT INTO new_book_interactions (user_id, book_id, interaction_type, score, created_at, updated_at)
                       SELECT user_id, book_id, interaction_type, 1, created_at, updated_at
                       FROM book_interactions");

        // Drop the old table
        Schema::dropIfExists('book_interactions');

        // Rename the new table to the original name
        Schema::rename('new_book_interactions', 'book_interactions');
    }

    public function down()
    {
        Schema::dropIfExists('book_interactions');
    }
};