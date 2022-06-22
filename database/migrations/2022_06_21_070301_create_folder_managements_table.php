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
        Schema::create('folder_managements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')->constrained('message_connections')->cascadeOnDelete();
            $table->foreignId('folder_creator_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->bigInteger('category_id')->nullable();
            $table->bigInteger('folder_parent_id')->nullable()->default(0);
            $table->text('folder_path')->nullable();
            $table->text('folder_path_url')->nullable();
            $table->text('folder_name')->nullable();
            $table->longText('folder_url')->nullable();
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
        Schema::dropIfExists('folder_managements');
    }
};
