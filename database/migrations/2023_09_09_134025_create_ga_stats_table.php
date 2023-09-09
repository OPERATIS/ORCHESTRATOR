<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGaStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ga_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('connect_id')->index();
            $table->unsignedBigInteger('impressions');
            $table->unsignedBigInteger('pageviews');
            $table->unsignedBigInteger('unique_pageviews');
            $table->unsignedBigInteger('ad_clicks');
            $table->float('ad_cost');
            $table->unsignedBigInteger('unique_table_id')->index();
            $table->timestamp('start_period')->index();
            $table->timestamp('end_period')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ga_stats');
    }
}
