<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddsStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adds_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('connect_id')->index();
            $table->unsignedBigInteger('clicks');
            $table->unsignedBigInteger('impressions');
            $table->float('spend');
            $table->unsignedBigInteger('unique_clicks');
            $table->unsignedBigInteger('ad_id')->index();
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
        Schema::dropIfExists('adds_stats');
    }
}
