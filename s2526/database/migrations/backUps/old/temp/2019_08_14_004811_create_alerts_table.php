<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('destino_user_id')->unsigned();
            $table->string('mensaje');
            $table->enum('tipo', ['primary',
                                    'secondary',
                                    'success',
                                    'info',
                                    'warning',
                                    'danger',
                                    'light',
                                    'dark',
                                    'default'])->default('default');
            $table->enum('estado',['sent','received','read'])->default('sent');
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
        Schema::dropIfExists('alerts');
    }
}
