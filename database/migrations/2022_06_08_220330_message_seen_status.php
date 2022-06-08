<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_seen_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_connection_id')->constrained('message_connections')->cascadeOnDelete();
            $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('seen_by_receiver')->default(0);
            $table->timestamp('seen_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_seen_status');
    }
};
