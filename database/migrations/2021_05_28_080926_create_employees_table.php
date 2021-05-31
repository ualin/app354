<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('email');
            $table->date('birthday');
            $table->boolean('is_manager');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();

            $table->foreign('department_id')
            ->references('id')->on('departments')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('room_id')
            ->references('id')->on('rooms')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
