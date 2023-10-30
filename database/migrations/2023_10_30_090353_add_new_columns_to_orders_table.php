<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('canceled_at')->nullable();
            $table->float('total_discounts')->default(0);
            $table->jsonb('discount_codes')->nullable();
            $table->jsonb('payment_gateway_names')->nullable();
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
            $table->dropColumn('canceled_at');
            $table->dropColumn('total_discounts');
            $table->dropColumn('discount_codes');
            $table->dropColumn('payment_gateway_names');
        });
    }
}
