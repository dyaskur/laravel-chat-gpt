<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // e.g., google_chat, slack, discord
            $table->string('external_id')->unique(); // External user ID in the integration
            $table->string('external_email')->nullable(); // Email used in the external platform
            $table->string('default_model')->nullable(); // Email used in the external platform
            $table->json('metadata')->nullable(); // Store extra details if needed
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_integrations');
    }
};
