<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamp('period')->index();
            $table->unsignedBigInteger('reach');
            $table->unsignedBigInteger('l');
            $table->float('p');
            $table->float('pu');
            $table->float('d');
            $table->float('q1');
            $table->unsignedBigInteger('cls');
            $table->float('returns');
            $table->float('q');
            $table->float('ltv');
            $table->float('r');
            $table->float('c');
            $table->float('c1');
            $table->unsignedBigInteger('count_customers');
            $table->unsignedBigInteger('ads_cls');
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
        Schema::dropIfExists('metrics');
    }
}
