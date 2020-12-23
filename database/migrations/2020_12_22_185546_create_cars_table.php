<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('first_word', 5);
            $table->string('license', 10);
            $table->unsignedTinyInteger('is_big')->default(0);
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
            $table->unique([
                'first_word',
                'license'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars');
    }
}
