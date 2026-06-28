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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            $table->foreignId('from')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->text('attachement_url')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_read')->default(false);
            $table->integer('deleted_at_by_user_1')->nullable();
            $table->integer('deleted_at_by_user_2')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('messages');
    }
};
