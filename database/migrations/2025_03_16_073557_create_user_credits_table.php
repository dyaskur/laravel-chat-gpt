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
        Schema::create('user_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('balance')->default(100); // Default credit amount
            $table->enum('reset_type', ['daily', 'weekly'])->default('daily'); // Reset frequency
            $table->string('reset_day')->nullable(); // If weekly, store the day (e.g., 'Monday')
            $table->timestamp('last_reset')->nullable(); // Last reset time
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_credits');
    }
};
