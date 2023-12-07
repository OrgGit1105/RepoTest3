<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('status')->default(1)->change();
            $table->integer('gender')->after('email')->nullable()->comment('0:male, 1:female');
            $table->date('birthday')->after('gender')->nullable();
            $table->string('address')->after('birthday')->nullable();
            $table->string('telephone')->after('address')->nullable();
            $table->date('entry_date')->after('telephone')->nullable();
            $table->string('slack_id')->after('entry_date')->nullable();
            $table->string('skype_id')->after('slack_id')->nullable();
            $table->string('github_id')->after('skype_id')->nullable();
            $table->renameColumn('role_id', 'viam_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('status')->change();
            $table->renameColumn('viam_user_id', 'role_id');
            $table->dropColumn('gender');
            $table->dropColumn('birthday');
            $table->dropColumn('address');
            $table->dropColumn('telephone');
            $table->dropColumn('entry_date');
            $table->dropColumn('slack_id');
            $table->dropColumn('skype_id');
            $table->dropColumn('github_id');
        });
    }
}
