<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDigitacoFilesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('digitaco_files', function (Blueprint $table) {
      $table->id();
      $table->date('getting_date')->nullable();
      $table->string('file_name_data_point', 500)->nullable();
      $table->string('file_path_data_point', 1000)->nullable();
      $table->string('file_name_data_driving', 500)->nullable();
      $table->string('file_path_data_driving', 1000)->nullable();
      $table->integer('status')->nullable();
      $table->integer('created_by')->nullable();
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
    Schema::dropIfExists('digitaco_files');
  }
}
