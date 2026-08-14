<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_events', function (Blueprint $table) {
            $table->id();
            $table->string('event')->index();
            $table->nullableMorphs('subject');
            $table->foreignId('actor_user_id')->nullable()->index();
            $table->json('recipient_ids')->nullable();
            $table->integer('channel_count')->default(0);
            $table->string('driver')->default('reverb');
            $table->boolean('delivered')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_events');
    }
};
