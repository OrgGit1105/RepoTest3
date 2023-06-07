<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('data_managements', function (Blueprint $table) {
        $table->id();
        $table->string('employee_code')->nullable()->unique();
        $table->string('staffs_name', 225)->nullable();
        $table->integer('joining_age_company')->nullable();
        $table->date('date_joining_company')->nullable();
        $table->date('date_out_company')->nullable();
        $table->tinyInteger('spouse')->nullable()->comment("0: No | 1: Yes");
        $table->integer('dependents')->nullable();
        $table->integer('worked_year')->nullable();
//        $table->unsignedtinyInteger('tolal_work_did')->nullable();
        $table->tinyInteger('final_education')->nullable()->comment("0: Primary school | 1: Junior high school | 2: High school | 3: Vocational school | 4: College of technology | 5: Junior college | 6: University");
        $table->integer('shortest_service')->nullable();
//        $table->unsignedBigInteger('company_branch_id')->nullable();
//        $table->foreign('company_branch_id')->references('id')->on('company_branchs')
//          ->onUpdate('cascade')->onDelete('cascade');
        $table->integer('total_worked')->nullable();
        $table->string('company_branch', 250)->nullable();
//        $table->Integer('created_by')->nullable();
//        $table->Integer('updated_by')->nullable();
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
    Schema::dropIfExists('data_managements');
  }
}

