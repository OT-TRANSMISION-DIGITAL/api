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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fechaHoraSolicitud');
            $table->dateTime('fechaHoraLlegada')->nullable();
            $table->dateTime('fechaHoraSalida')->nullable();
            $table->string('persona_solicitante');
            $table->string('puesto')->nullable();
            $table->string('direccion');
            $table->string('estatus');
            $table->string('firma')->nullable();
            $table->string('coorLlegada')->nullable();
            $table->string('coorSalida')->nullable();
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->unsignedBigInteger('tecnico_id');
            $table->foreign('tecnico_id')->references('id')->on('users');
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->foreign('sucursal_id')->references('id')->on('sucursales');
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
        Schema::dropIfExists('ordenes');
    }
};
