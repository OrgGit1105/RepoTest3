<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeColumnToRdsPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rds_permission', function (Blueprint $table) {
            $table->tinyInteger('type')->comment('1:data, 2: Structure, 3: Administration, 4: All');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rds_permission', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
