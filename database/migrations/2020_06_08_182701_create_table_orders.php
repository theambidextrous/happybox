<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOrders extends Migration
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
            $table->string('order_id')->unique();
            $table->string('customer_buyer');
            $table->text('order_evouchers')->nullable();
            $table->text('order_pvouchers')->nullable();
            $table->text('order_string');
            $table->string('subtotal');
            $table->string('shipping_cost');
            $table->string('order_totals');
            $table->boolean('paid')->default(false);
            $table->string('payment_method')->nullable();
            $table->integer('payment_status')->default(0);
            $table->text('payment_string')->nullable();
            $table->boolean('shipped')->default(false);
            $table->integer('shipment_status')->default(0);
            $table->text('shipment_string')->nullable();
            $table->text('token');
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
