<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatabasePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('database_permission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('database_id');
            $table->unsignedBigInteger('rds_permission_id');

            $table->foreign('database_id')->references('id')->on('database')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('rds_permission_id')->references('id')->on('rds_permission')
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
        Schema::dropIfExists('database_permission');
    }
}
