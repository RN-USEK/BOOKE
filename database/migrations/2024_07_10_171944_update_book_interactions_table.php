<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop the existing index
        Schema::table('book_interactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'book_id', 'interaction_type']);
        });

        // Change the interaction_type column to a string
        DB::statement("ALTER TABLE book_interactions MODIFY interaction_type VARCHAR(255)");

        // Add the new values to the interaction_type column
        DB::statement("ALTER TABLE book_interactions MODIFY interaction_type ENUM('view', 'purchase', 'review', 'wishlist')");

        // Add the score column and unique constraint
        Schema::table('book_interactions', function (Blueprint $table) {
            $table->integer('score')->after('interaction_type');
            $table->unique(['user_id', 'book_id', 'interaction_type']);
        });
    }

    public function down()
    {
        Schema::table('book_interactions', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'book_id', 'interaction_type']);
            $table->dropColumn('score');
        });

        // Revert the interaction_type column
        DB::statement("ALTER TABLE book_interactions MODIFY interaction_type ENUM('view', 'purchase')");

        // Re-add the original index
        Schema::table('book_interactions', function (Blueprint $table) {
            $table->index(['user_id', 'book_id', 'interaction_type']);
        });
    }
};