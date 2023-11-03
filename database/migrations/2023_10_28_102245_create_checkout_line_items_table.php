<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckoutLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkout_line_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('integration_id')->index();
            // Local inner id
            $table->unsignedBigInteger('checkout_id')->index();
            $table->string('checkout_line_item_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('variant_id')->index();
            $table->float('price');
            $table->float('line_price');
            $table->float('quantity')->nullable();
            $table->boolean('gift_card');
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
        Schema::dropIfExists('checkout_line_items');
    }
}
