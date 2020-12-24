<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->char('username',11)->unique();
            $table->string('password')->default('');
            $table->unsignedBigInteger('level_id')->default(0)->index();
            $table->unsignedInteger('surplus_days')->default(0);
            $table->string('nickname')->default('');
            $table->string('avatar')->default('');
            $table->unsignedTinyInteger('gender')->default(0);
            $table->timestamps();
            $table->date('end_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
