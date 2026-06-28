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
        Schema::create('near_by_supports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('contact_name')->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('address');
            $table->text('services');
            $table->text('availability');
            $table->unsignedBigInteger('university_id');
            $table->foreign('university_id')->references('id')->on('universities');
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
        Schema::dropIfExists('near_by_supports');
    }
};
