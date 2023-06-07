<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
          $table->id();
          $table->dateTime('interview_date');
//          $table->tinyInteger('interview_branch');
          $table->string('candidate_name', 225);
          $table->unsignedtinyInteger('joining_age');
          $table->tinyInteger('spouse')->nullable()->comment("0: No | 1: Yes");
          $table->unsignedtinyInteger('dependents')->nullable();
          $table->unsignedtinyInteger('worked_years');
          $table->tinyInteger('final_education')->nullable()->comment("0: Primary school | 1: Junior high school | 2: High school | 3: Vocational school | 4: College of technology | 5: Junior college | 6: University");
          $table->unsignedtinyInteger('shortest_service')->nullable();
          $table->unsignedBigInteger('company_branch_id')->nullable();
          $table->foreign('company_branch_id')->references('id')->on('company_branchs')
            ->onUpdate('cascade')->onDelete('cascade');
          $table->tinyInteger('is_accepted')->default(0);
          $table->Integer('created_by')->nullable();
          $table->Integer('updated_by')->nullable();
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
        Schema::dropIfExists('enrollments');
    }
}
