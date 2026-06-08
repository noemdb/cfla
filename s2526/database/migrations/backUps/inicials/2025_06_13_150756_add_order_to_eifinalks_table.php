<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderToEifinalksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eifinalks', function (Blueprint $table) {
            $table->integer('order')->nullable()->unsigned()->comment('Orden')->after('id'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eifinalks', function (Blueprint $table) {
            Schema::dropIfExists('order');
        });
    }
}
