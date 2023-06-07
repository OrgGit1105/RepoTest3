<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiskScoresTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('risk_scores', function (Blueprint $table) {
      $table->id();
      $table->date('month_year')->nullable();
      $table->string('employee_id')->nullable();
      $table->float('retirement_score', 10 , 5)->nullable();

      $table->softDeletes();
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
    Schema::dropIfExists('risk_scores');
  }
}

