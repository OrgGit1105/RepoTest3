<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloumnTypeDateAndNumberDayToArrivingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('arriving_reports', function (Blueprint $table) {
            $table->integer('type_date')->nullable()->after('registration_type')->comment('work, remote, off');
            $table->decimal('number_day', 8, 2)->nullable()->after('registration_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('arriving_reports', function (Blueprint $table) {
            $table->dropColumn('type_date');
            $table->dropColumn('number_day');
        });
    }
}
