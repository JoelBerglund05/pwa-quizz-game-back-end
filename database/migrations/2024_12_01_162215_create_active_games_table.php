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
            $table->integer("user_id_1")->nullable(false);
            $table->integer("user_id_2")->nullable(true);
            $table->string("user_name_1")->nullable(false);
            $table->string("user_name_2")->nullable(true);
            $table->integer("user_points_1")->nullable(false);
            $table->integer("user_points_2")->nullable(false);
            $table->integer("user_turn")->nullable(false);
            $table->string("question_1")->nullable(true);
            $table->string("question_2")->nullable(true);
            $table->string("question_3")->nullable(true);
            $table->string("category")->nullable(true);
            $table->integer("rounds")->nullable(false);
            $table->boolean("user_1_has_answerd_question")->nullable(true);
            $table->boolean("user_2_has_answerd_question")->nullable(true);
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
