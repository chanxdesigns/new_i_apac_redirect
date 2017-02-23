<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStarttimeColumnInRespcounter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resp_counters', function ($table) {
           $table->timestamp('starttime')->after('IP');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resp_counters', function ($table) {
           $table->dropColumn('starttime');
        });
    }
}
