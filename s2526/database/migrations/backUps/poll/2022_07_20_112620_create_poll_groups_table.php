<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePollGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_groups', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name')->comment('Nombre del grupo');
            $table->string('description')->comment('Descripción del grupo');
            $table->timestamps();
        });

        DB::table("poll_groups")
        ->insert([
            "name" => "Representantes [-5toAño]",
            "code" => "GR1",
            "description" => "Todos los representantes excepto 5toAño"
        ]);

        DB::table("poll_groups")
            ->insert([
                "name" => "Representantes [Todos]",
                "code" => "GR2",
                "description" => "Todos los representantes"
            ]);

        DB::table("poll_groups")
            ->insert([
                "name" => "Estudiantes [Todos]",
                "code" => "GR3",
                "description" => "Todos los estudiantes"
            ]);

        DB::table("poll_groups")
            ->insert([
                "name" => "Estudiantes [-5toAño]",
                "code" => "GR4",
                "description" => "Todos los estudiantes excepto 5toAño"
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_groups');
    }
}
