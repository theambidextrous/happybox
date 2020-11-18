<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderCronsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_crons', function (Blueprint $table) {
            //'order_id', 'customer_buyer', 'box_voucher', 'order_meta', 'deliver_to', 'sendy_log', 'is_send',
            $table->id();
            $table->string('order_id', 30);
            $table->string('customer_buyer', 30);
            $table->string('box_voucher', 30);
            $table->text('order_meta');
            $table->text('deliver_to');
            $table->text('sendy_log')->nullable();
            $table->boolean('is_send')->default(false);
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
        Schema::dropIfExists('order_crons');
    }
}
