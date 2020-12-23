<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->char('no',18)->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('car_id')->index();
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedBigInteger('enter_barrier_id');
            $table->unsignedBigInteger('out_barrier_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->decimal('price')->nullable();
            $table->timestamps();
            $table->timestamp('outed_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
