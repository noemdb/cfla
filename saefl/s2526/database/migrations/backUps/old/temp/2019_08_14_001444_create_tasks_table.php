<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('codigo',32);
            $table->string('descripcion');
            $table->enum('tipo', ['primary',
                                    'secondary',
                                    'success',
                                    'info',
                                    'warning',
                                    'danger',
                                    'light',
                                    'dark',
                                    'default'])->default('default');
            $table->string('evento',32)->nullable();
            $table->enum('estado',['INICIADA','REPROGRAMADA','FINALIZADA'])->default('INICIADA');
            $table->date('finicial');
            $table->date('ffinal');

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
