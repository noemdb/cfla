<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sequences', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->string('ci_representant');
            $table->string('step')->nullable();
            $table->string('card_number')->nullable();
            $table->string('type_ci')->nullable();
            $table->string('cvc')->nullable();
            $table->string('expiration_month')->nullable();
            $table->string('expiration_year')->nullable();
            $table->string('date_expiration')->nullable();
            $table->string('account_type')->nullable();
            $table->string('card_pin')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_type_holder')->nullable();
            $table->string('holder_name')->nullable();
            $table->string('holder_id_doc')->nullable();
            $table->string('holder_id')->nullable();
            $table->string('access_token')->nullable();
            $table->string('date_expires')->nullable();
            $table->string('token_bank')->nullable();
            $table->string('ammount_pay')->nullable();
            $table->string('ammount_pay_exchange')->nullable();
            $table->string('exchange_ammount')->nullable();

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
        Schema::dropIfExists('sequences');
    }
}
