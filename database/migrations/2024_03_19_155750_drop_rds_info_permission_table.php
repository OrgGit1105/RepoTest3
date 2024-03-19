<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropRdsInfoPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('rds_info_permission');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('rds_info_permission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rds_info_id');
            $table->unsignedBigInteger('rds_permission_id');
            $table->integer('max_queries_per_hour')->default(0);
            $table->integer('max_updates_per_hour')->default(0);
            $table->integer('max_connections_per_hour')->default(0);
            $table->integer('max_user_connections')->default(0);


            $table->foreign('rds_info_id')->references('id')->on('rds_info')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('rds_permission_id')->references('id')->on('rds_permission')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->timestamps();
        });
    }
}
