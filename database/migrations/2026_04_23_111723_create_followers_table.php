<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->id();
            // Quem segue
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            // Quem é seguido
            $table->foreignId('followed_id')->constrained('users')->onDelete('cascade');

            // Impede que alguém siga a mesma pessoa duas vezes
            $table->unique(['follower_id', 'followed_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
