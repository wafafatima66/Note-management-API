<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_task_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')->constrained('message_connections')->cascadeOnDelete();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_task_status');
    }
};
