<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogdbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logdbs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            // $table->string('action')->nullable();
            $table->enum('action', [
                'created',
                'deleted',
                'restored',
                'updated'
            ]);
            $table->enum('tipo', ['primary','success', 'info', 'warning', 'danger','default'])->default('default');
            $table->string('model_class')->nullable();
            $table->string('model_id')->nullable();
            $table->text('data')->nullable();
            $table->string('ip')->nullable();
            $table->string('pathInfo')->nullable();
            $table->string('url')->nullable();
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
        Schema::dropIfExists('logdbs');
    }
}
