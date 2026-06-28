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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_1')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_2')->constrained('users')->onDelete('cascade');
            $table->boolean('block_at_by_user_1')->nullable();
            $table->boolean('block_at_by_user_2')->nullable();
            $table->string('type')->default('general');
            $table->softDeletes();
            $table->string('status')->default('created');
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
        Schema::dropIfExists('chats');
    }
};
