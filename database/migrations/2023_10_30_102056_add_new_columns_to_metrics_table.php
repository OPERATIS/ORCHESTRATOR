<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metrics', function (Blueprint $table) {
            $table->jsonb('map')->nullable();
            $table->float('cpd')->default(0);
            $table->float('ccr')->default(0);
            $table->float('car')->default(0);
            $table->float('grccr')->default(0);
            $table->float('aov')->default(0);
            $table->float('dur')->default(0);
            $table->float('mppr')->default(0);
            $table->float('rr')->default(0);
            $table->float('ct')->nullable();
            $table->float('ccur')->default(0);
            $table->jsonb('pmd')->nullable();
            $table->float('cv')->default(0);
            $table->float('gcur')->default(0);
            $table->float('ttv')->default(0);
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
            $table->dropColumn('map');
            $table->dropColumn('cpd');
            $table->dropColumn('ccr');
            $table->dropColumn('car');
            $table->dropColumn('grccr');
            $table->dropColumn('aov');
            $table->dropColumn('dur');
            $table->dropColumn('mppr');
            $table->dropColumn('rr');
            $table->dropColumn('ct');
            $table->dropColumn('ccur');
            $table->dropColumn('pmd');
            $table->dropColumn('cv');
            $table->dropColumn('gcur');
            $table->dropColumn('ttv');
        });
    }
}
