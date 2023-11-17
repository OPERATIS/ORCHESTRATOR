<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleForLineItemsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkout_line_items', function (Blueprint $table) {
            $table->string('title')->nullable();
        });

        Schema::table('order_line_items', function (Blueprint $table) {
            $table->string('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkout_line_items', function (Blueprint $table) {
            $table->dropColumn('title');
        });

        Schema::table('order_line_items', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
}
