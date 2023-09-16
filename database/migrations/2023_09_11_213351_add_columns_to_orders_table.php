<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->float('total_line_items_price')->default(0);
            $table->unsignedInteger('count_line_items')->default(0);
            $table->unsignedInteger('count_refund_line_items')->default(0);
            $table->float('total_refund_line_items_price')->default(0);
            $table->string('reference')->nullable();
            $table->string('referring_site')->nullable();
            $table->boolean('ads')->default(0)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('total_line_items_price');
            $table->dropColumn('count_line_items');
            $table->dropColumn('count_refund_line_items');
            $table->dropColumn('total_refund_line_items_price');
            $table->dropColumn('reference');
            $table->dropColumn('referring_site');
            $table->dropColumn('ads');
        });
    }
}
