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
        Schema::create('national_help_lines', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('contact_name')->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('address');
            $table->text('services');
            $table->text('availability');

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
        Schema::dropIfExists('national_help_lines');
    }
};
