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
        Schema::create('google_chat_spaces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('space id')->unique();
            $table->string('display_name');
            $table->bigInteger('credits')->default(0);
            $table->string('space_url')->nullable();
            $table->boolean('is_thread')->nullable();
            $table->boolean('save_history');
            $table->json('metadata');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_chat_spaces');
    }
};
