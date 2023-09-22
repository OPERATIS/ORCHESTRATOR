<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalyzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analyzes', function (Blueprint $table) {
            $table->id();
            $table->float('c_ucl');
            $table->float('c_lcl');
            $table->float('l_ucl');
            $table->float('l_lcl');
            $table->float('p_ucl');
            $table->float('p_lcl');
            $table->float('q_ucl');
            $table->float('q_lcl');
            $table->string('period')->index();
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
        Schema::dropIfExists('analyzes');
    }
}
