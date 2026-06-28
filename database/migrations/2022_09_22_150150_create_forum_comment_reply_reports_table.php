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
        Schema::create('forum_comment_reply_reports', function (Blueprint $table) {
            $table->id();
            $table->text('reason_for_report');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('reported_by')->constrained('users');
            $table->foreignId('comment_id')->constrained('forum_comment_replies')->onDelete('cascade');
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
        Schema::dropIfExists('forum_comment_reply_reports');
    }
};
