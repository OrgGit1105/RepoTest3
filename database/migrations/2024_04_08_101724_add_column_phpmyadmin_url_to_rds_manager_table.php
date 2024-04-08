<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPhpmyadminUrlToRdsManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rds_manager', function (Blueprint $table) {
            $table->string('phpmyadmin_url')->after('ec2_username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rds_manager', function (Blueprint $table) {
            $table->dropColumn('phpmyadmin_url');
        });
    }
}
