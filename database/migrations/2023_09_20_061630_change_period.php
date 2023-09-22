<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangePeriod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metrics', function (Blueprint $table) {
            DB::table('metrics')->truncate();
            $table->string('period')->change();
            $table->timestamp('start_period')->index();
            $table->timestamp('end_period')->index();
        });

        Schema::table('aggregation_ga_stats', function (Blueprint $table) {
            DB::table('aggregation_ga_stats')->truncate();
            $table->string('period')->change();
            $table->timestamp('start_period')->index();
            $table->timestamp('end_period')->index();
        });

        Schema::table('aggregation_fb_stats', function (Blueprint $table) {
            DB::table('aggregation_fb_stats')->truncate();
            $table->string('period')->change();
            $table->timestamp('start_period')->index();
            $table->timestamp('end_period')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metrics', function (Blueprint $table) {
            $table->timestamp('period')->index()->change();
            $table->dropColumn('start_period');
            $table->dropColumn('end_period');
        });

        Schema::table('aggregation_ga_stats', function (Blueprint $table) {
            $table->timestamp('period')->index()->change();
            $table->dropColumn('start_period');
            $table->dropColumn('end_period');
        });

        Schema::table('aggregation_fb_stats', function (Blueprint $table) {
            $table->timestamp('period')->index()->change();
            $table->dropColumn('start_period');
            $table->dropColumn('end_period');
        });
    }
}
