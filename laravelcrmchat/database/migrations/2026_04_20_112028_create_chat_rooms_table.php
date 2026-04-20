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
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // For group chats, null for direct messages
            $table->boolean('is_direct')->default(false); // true for 1-on-1, false for group
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['is_direct', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
