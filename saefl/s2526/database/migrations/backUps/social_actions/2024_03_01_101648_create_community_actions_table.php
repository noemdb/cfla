<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_actions', function (Blueprint $table) {
            $table->id()->comment('Identificador'); // Auto-incrementing primary key
            $table->unsignedBigInteger('user_id')->comment('Usuario')->index();
            $table->unsignedBigInteger('grado_id')->comment('Grado')->index();
            $table->string('title')->comment('Título');
            $table->text('description')->comment('Descripción');
            $table->text('observations')->nullable()->comment('Observaciones sobre la actividad (opcional)');
            $table->date('date')->comment('Fecha');
            $table->unsignedTinyInteger('duration')->nullable()->comment('Duración (horas)');
            $table->boolean('status')->default(true)->comment('Estado (activa o inactiva)');
            $table->enum('type', ['individual', 'group'])->default('group')->comment('Tipo de actividad (individual o grupal)');
            $table->string('entity_benefic')->nullable()->comment('Entidad beneficiada por la actividad (opcional)');
            $table->string('location')->nullable()->comment('Lugar donde se realiza la actividad (opcional)');
            $table->text('required')->nullable()->comment('Requisitos para participar en la actividad (opcional)');
            $table->string('image', 255)->nullable()->comment('Imagen (opcional)');
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
        Schema::dropIfExists('community_actions');
    }
}
