<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnToRdsManagerTable extends Migration
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
            $table->integer('type')->default(1)->comment('0:server_local|1:server_other')->after('phpmyadmin_url');
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
            $table->dropColumn('type');
        });
    }
}
