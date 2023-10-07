<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameConnectsToIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('connects', 'integrations');

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('connect_id', 'integration_id');
        });

        Schema::table('fb_stats', function (Blueprint $table) {
            $table->renameColumn('connect_id', 'integration_id');
        });

        Schema::table('ga_stats', function (Blueprint $table) {
            $table->renameColumn('connect_id', 'integration_id');
        });

        Schema::table('aggregation_ga_stats', function (Blueprint $table) {
            $table->renameColumn('connect_id', 'integration_id');
        });

        Schema::table('aggregation_fb_stats', function (Blueprint $table) {
            $table->renameColumn('connect_id', 'integration_id');
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->renameColumn('connect_id', 'integration_id');
        });

        Schema::table('ga_profiles', function (Blueprint $table) {
            $table->renameColumn('connect_id', 'integration_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('integrations', 'connects');

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('integration_id', 'connect_id');
        });

        Schema::table('fb_stats', function (Blueprint $table) {
            $table->renameColumn('integration_id', 'connect_id');
        });

        Schema::table('ga_stats', function (Blueprint $table) {
            $table->renameColumn('integration_id', 'connect_id');
        });

        Schema::table('aggregation_ga_stats', function (Blueprint $table) {
            $table->renameColumn('integration_id', 'connect_id');
        });

        Schema::table('aggregation_fb_stats', function (Blueprint $table) {
            $table->renameColumn('integration_id', 'connect_id');
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->renameColumn('integration_id', 'connect_id');
        });

        Schema::table('ga_profiles', function (Blueprint $table) {
            $table->renameColumn('integration_id', 'connect_id');
        });
    }
}
