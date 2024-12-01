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
        Schema::create("active_games", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer("user_id_1");
            $table->integer("user_id_2");
            $table->string("user_points_1");
            $table->string("user_points_2");
            $table->integer("user_turn");
            $table->string("question_1");
            $table->string("question_2");
            $table->string("question_3");
            $table->string("category");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_games');
    }
};
