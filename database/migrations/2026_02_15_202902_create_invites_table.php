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
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('token')->unique();
            $table->string('table_number');
            $table->boolean('is_used')->default(false);
            $table->string('invitee_name')->nullable();
            $table->string('invitee_phone')->nullable();
            $table->boolean('has_plus_one')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};
