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
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duel_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('number');
            $table->foreignId('user_card_id')->constrained('cards');
            $table->foreignId('opponent_card_id')->constrained('cards');
            $table->integer('user_points')->default(0);
            $table->integer('opponent_points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};
