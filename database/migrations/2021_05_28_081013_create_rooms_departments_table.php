<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms_departments', function (Blueprint $table) {
            // $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('department_id');
            $table->boolean('deleted')->default(0);

            $table->foreign('department_id')
            ->references('id')->on('departments')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('room_id')
            ->references('id')->on('rooms')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->unique(['room_id', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms_departments');
    }
}
