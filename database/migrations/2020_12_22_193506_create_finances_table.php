<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->char('no',19)->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('car_id')->index();
            $table->unsignedInteger('level_id')->index();
            $table->decimal('price')->nullable();
            $table->boolean('status')->unsigned()->default(false);
            $table->timestamps();
            $table->timestamp('payed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finances');
    }
}
