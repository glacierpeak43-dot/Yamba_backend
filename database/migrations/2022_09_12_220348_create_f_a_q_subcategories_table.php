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
        if (!Schema::hasTable('f_a_q_subcategories')) {
            Schema::create('f_a_q_subcategories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('key');
                $table->foreignId('f_a_q_subcategories')->constrained('f_a_q_categories')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_a_q_subcategories');
    }
};
