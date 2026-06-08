<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class InsertDataMetodoPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metodo_pagos', function (Blueprint $table) {
            //
        });

        $id = DB::table("metodo_pagos")
            ->insertGetId([
                "name" => "Botón de Pago",
                "code" => "BTNCC",
                "is_public" => "false",
                "is_intern" => "true",
                "status_active" => true,
                "observations" => "Botón de Pago",
            ]);

        // DB::table('metodo_pagos')->delete($id);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metodo_pagos', function (Blueprint $table) {
            //
        });

        $data = DB::table('metodo_pagos')
            ->select('metodo_pagos.*')
            ->where('name','Botón de Pago')
            ->where('code','BTNCC')
            ->first();
        DB::table('metodo_pagos')->delete($data->id);
    }
}

/*

        name    varchar(191)    utf8mb4_unicode_ci      No  None    Nombres     Change Change   Drop Drop   
    3   code    varchar(24) utf8mb4_unicode_ci      Yes NULL            Change Change   Drop Drop   
    4   is_public   enum('true', 'false')   utf8mb4_unicode_ci      No  false   Estado      Change Change   Drop Drop   
    5   is_intern   enum('true', 'false')   utf8mb4_unicode_ci      No  false   Estado      Change Change   Drop Drop   
    6   status_active   tinyint(1)          No  1           Change Change   Drop Drop   
    7   observations    varchar(191)    utf8mb4_unicode_ci      Yes NULL    Observaciones del Metodo de Pago        Change Change   Drop Drop   
    8   created_at  timestamp           Yes NULL            Change Change   Drop Drop   
    9   updated_at

*/