<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRdsInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rds_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('viam_user_id');
            $table->unsignedBigInteger('rds_manager_id');

            $table->foreign('viam_user_id')->references('id')->on('viam_users')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('rds_manager_id')->references('id')->on('rds_manager')
                ->onUpdate('restrict')
                ->onDelete('restrict');
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
        Schema::dropIfExists('rds_info');
    }
}
