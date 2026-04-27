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
    Schema::create('follows', function (Blueprint $table) {
        $table->id();
        // follower_id: quem está seguindo
        $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
        // following_id: quem está sendo seguido
        $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
        
        $table->unique(['follower_id', 'following_id']);
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
