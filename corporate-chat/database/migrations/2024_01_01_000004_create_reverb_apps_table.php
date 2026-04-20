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
        Schema::create('reverb_apps', function (Blueprint $table) {
            $table->id();
            $table->string('app_id');
            $table->string('secret');
            $table->string('name')->nullable();
            $table->integer('capacity')->default(0);
            $table->boolean('enable_statistics')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reverb_apps');
    }
};
