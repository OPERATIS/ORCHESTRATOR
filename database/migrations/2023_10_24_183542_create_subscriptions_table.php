<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('stripe_id')->index();
            $table->timestamp('ends_at')->nullable();
            $table->float('price')->nullable();
            $table->unsignedInteger('days')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('next_billing_period_at')->nullable();
            $table->timestamp('user_subscribe_to')->nullable();
            $table->boolean('send_letter_after_start')->default(0);
            $table->boolean('send_letter_after_end')->default(0);
            $table->string('manual_status')->nullable();
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
        Schema::dropIfExists('subscriptions');
    }
}
