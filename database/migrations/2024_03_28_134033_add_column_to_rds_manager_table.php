<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToRdsManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rds_manager', function (Blueprint $table) {
            $table->string('key_file')->after('port')->nullable();
            $table->string('ec2_ip_address')->after('key_file')->nullable();
            $table->string('ec2_username')->after('ec2_ip_address')->nullable();
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
            $table->dropColumn('key_file');
            $table->dropColumn('ec2_ip_address');
            $table->dropColumn('ec2_username');
        });
    }
}
