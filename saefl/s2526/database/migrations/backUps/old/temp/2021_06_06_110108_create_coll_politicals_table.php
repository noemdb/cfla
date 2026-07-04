<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollPolicalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coll_politicals', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name')->comment('Nombre');
            $table->string('code',10)->comment('Código');
            $table->integer('user_id')->unsigned();
            $table->string('description')->nullable()->comment('Descripción');
            $table->date('finicial')->nullable()->comment('Fecha de inicio');
            $table->date('ffinal')->nullable()->comment('Fecha de fin');
            $table->enum('status',['true','false'])->default('true')->comment('Estado de aprobación');
            $table->enum('status',['true','false'])->default('true')->comment('Estado de aprobación');
            $table->enum('canon',['debts','levels','imployeds','adviders','collaborators'])->default('false')->comment('Estado de aprobación');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coll_politicals');
    }
}
