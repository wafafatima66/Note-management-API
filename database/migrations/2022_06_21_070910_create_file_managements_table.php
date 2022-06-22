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
        Schema::create('file_managements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')->constrained('message_connections')->cascadeOnDelete();
            $table->foreignId('uploader_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->bigInteger('category_id')->nullable();
            $table->bigInteger('folder_id')->nullable()->default(0);
            $table->text('file_name')->nullable();
            $table->longText('file_url')->nullable();
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
        Schema::dropIfExists('file_managements');
    }
};
