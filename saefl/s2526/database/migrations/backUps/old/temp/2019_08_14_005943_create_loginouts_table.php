<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loginouts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->enum('action', [
                'Registered',
                'Attempting',
                'LogAuthenticated',
                'LogSuccessfulLogin',
                'LogFailedLogin',
                'LogSuccessfulLogout',
                'LogLockout',
                'LogPasswordReset'
            ]);
            $table->enum('tipo', ['primary','success', 'info', 'warning', 'danger','default'])->default('default');
            $table->string('message', 500)->nullable();
            $table->text('context')->nullable();
            $table->text('extra')->nullable();
            $table->string('ip')->nullable();
            $table->string('view')->nullable();
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
        Schema::dropIfExists('loginouts');
    }
}
