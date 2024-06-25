<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnInOutTimeToHistoryEditReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('history_edit_reports', function (Blueprint $table) {
            $table->datetime('in_time')->change();
            $table->datetime('out_time')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('history_edit_reports', function (Blueprint $table) {
            $table->timestamp('in_time')->change();
            $table->timestamp('out_time')->change();
        });
    }
}
