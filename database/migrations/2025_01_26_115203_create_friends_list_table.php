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
        Schema::create('friends_list', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('email_1');
            $table->string('email_2');
            $table->string('name_1');
            $table->string('name_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friends_list');
    }
};
