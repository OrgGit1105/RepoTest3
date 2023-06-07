<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyBranchsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_branchs', function (Blueprint $table) {
            $table->id()->references('company_branch_id')->on('enrollments');
            $table->string('name', 250)->nullable();
            $table->string('address', 250)->nullable();
            $table->integer('role_id');
            $table->string('description', 512)->nullable();
            $table->integer('created_by_user')->nullable();
            $table->integer('updated_by_user')->nullable();
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
        Schema::dropIfExists('company_branchs');
    }
}
