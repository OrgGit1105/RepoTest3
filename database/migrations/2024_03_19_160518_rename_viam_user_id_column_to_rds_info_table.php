<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameViamUserIdColumnToRdsInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rds_info', function (Blueprint $table) {
            $table->dropForeign('rds_info_viam_user_id_foreign');
            $table->renameColumn('viam_user_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rds_info', function (Blueprint $table) {
            $table->dropForeign('rds_info_user_id_foreign');
            $table->renameColumn('user_id', 'viam_user_id');
            $table->foreign('viam_user_id')->references('id')->on('viam_users')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }
}
