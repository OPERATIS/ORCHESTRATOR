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
            $table->jsonb('l_map')->nullable();
            $table->float('l_cpd')->nullable();
            $table->float('l_ccr')->nullable();
            $table->float('car')->nullable();
            $table->float('c_car')->nullable();
            $table->float('c_grccr')->nullable();
            $table->float('p_aov')->nullable();
            $table->float('p_dur')->nullable();
            $table->float('p_mppr')->nullable();
            $table->float('q_rr')->nullable();
            $table->float('car_ct')->nullable();
            $table->float('p_ccur')->nullable();
            $table->jsonb('c_pmd')->nullable();
            $table->float('p_cv')->nullable();
            $table->float('q_gcur')->nullable();
            $table->float('car_ttv')->nullable();
            $table->jsonb('car_pmu')->nullable();
            $table->float('car_fpr')->nullable();
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
            $table->dropColumn('l_map');
            $table->dropColumn('l_cpd');
            $table->dropColumn('l_ccr');
            $table->dropColumn('car');
            $table->dropColumn('c_car');
            $table->dropColumn('c_grccr');
            $table->dropColumn('p_aov');
            $table->dropColumn('p_dur');
            $table->dropColumn('p_mppr');
            $table->dropColumn('q_rr');
            $table->dropColumn('car_ct');
            $table->dropColumn('p_ccur');
            $table->dropColumn('c_pmd');
            $table->dropColumn('p_cv');
            $table->dropColumn('q_gcur');
            $table->dropColumn('car_ttv');
            $table->dropColumn('car_pmu');
            $table->dropColumn('car_fpr');
        });
    }
}
