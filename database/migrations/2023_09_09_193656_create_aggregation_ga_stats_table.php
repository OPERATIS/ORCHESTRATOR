<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAggregationGaStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregation_ga_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('connect_id')->index();
            $table->unsignedBigInteger('impressions');
            $table->unsignedBigInteger('pageviews');
            $table->unsignedBigInteger('unique_pageviews');
            $table->unsignedBigInteger('ad_clicks');
            $table->float('ad_cost');
            $table->unsignedBigInteger('unique_table_id')->index();
            $table->timestamp('period')->index();
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
        Schema::dropIfExists('aggregation_ga_stats');
    }
}
