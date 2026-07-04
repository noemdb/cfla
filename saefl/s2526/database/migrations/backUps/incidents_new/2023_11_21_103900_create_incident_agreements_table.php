<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_agreements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('incident_id')->unsigned()->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->text('description', 191)->nullable();
            $table->text('observations', 191)->nullable();
            $table->boolean('status_notify')->default(false)->comment('Notificación');
            $table->date('date_notify_email')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('incident_id')->references('id')->on('incidents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_agreements');
    }
}
