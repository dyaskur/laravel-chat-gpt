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
        Schema::create('google_chat_spaces_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('google_chat_space_id')->constrained()->onDelete('cascade');
            $table->string('user_external_id')->comment('References external_id in user_integrations');
            $table->timestamps();

            $table->unique(['google_chat_space_id', 'user_external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_chat_spaces_users');
    }
};
