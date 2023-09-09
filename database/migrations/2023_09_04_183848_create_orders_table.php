<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('connect_id')->index();
            $table->unsignedBigInteger('order_id')->index();
            $table->timestamp('order_created_at')->index();
            $table->string('financial_status')->index();
            $table->unsignedBigInteger('order_number');
            $table->float('total_price');
            $table->unsignedBigInteger('customer_id')->index()->nullable();
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
        Schema::dropIfExists('orders');
    }
}
