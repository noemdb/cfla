<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagReportAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diag_report_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('diag_reports')->onDelete('cascade');

            // 'users' table uses increments (int), verified.
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('action')->comment('generated, edited, validated, signed, viewed');
            $table->text('details')->nullable();

            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

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
        Schema::dropIfExists('diag_report_audit_logs');
    }
}
