<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('integration_id')->index();
            $table->unsignedBigInteger('order_id')->index()->nullable();
            $table->timestamp('checkout_created_at')->index();
            $table->timestamp('checkout_completed_at')->index()->nullable();
            $table->unsignedBigInteger('customer_id')->index()->nullable();
            $table->float('total_price');
            $table->string('token')->index();
            $table->jsonb('gift_cards');
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
        Schema::dropIfExists('checkouts');
    }
}
