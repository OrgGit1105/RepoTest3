<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInstanceToPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->string('instance_id')->nullable()->after('type');
            $table->string('policy_arn')->nullable()->after('instance_id');
            $table->string('project_name')->nullable()->after('policy_arn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn('instance_id');
            $table->dropColumn('policy_arn');
            $table->dropColumn('project_name');
        });
    }
}
