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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('space id');
            $table->string('integration_id')->nullable();
            $table->string('integration_name')->nullable();
            $table->json('integration_metadata')->nullable();
            $table->string('description')->nullable();
            $table->bigInteger('coin_balance')->default(0);
            $table->timestamp('last_coin_reset')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
