<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGetMailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('get_mail_logs', function (Blueprint $table) {
          $table->id();
          $table->integer('status')->nullable();
          $table->text('message_log')->nullable();
          $table->string('mail_id')->nullable();
          $table->string('mail_subject')->nullable();
          $table->string('mail_from')->nullable();
          $table->string('mail_to')->nullable();
          $table->string('mail_attachment_file_name')->nullable();
          $table->string('mail_attachment_path')->nullable();
          $table->dateTime('mail_date')->nullable();
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
        Schema::dropIfExists('get_mail_logs');
    }
}
