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
        Schema::create('messages_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')->constrained('message_connections')->cascadeOnDelete();
            $table->bigInteger('parent_id')->nullable()->default(0);
            $table->foreignId('assignee_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('message_task_status')->cascadeOnDelete();
            $table->enum('type', ['epic', 'story', 'task', 'sub-task', 'bug'])->default('task');
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->dateTime('deadline')->nullable();
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
        Schema::dropIfExists('messages_tasks');
    }
};
