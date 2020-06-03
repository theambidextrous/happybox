<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable();
            $table->string('customer_buyer_id')->nullable();
            $table->string('customer_payment_method')->nullable();
            $table->string('box_delivery_address')->nullable();
            $table->string('box_purchase_date')->nullable();
            $table->string('box_validity_date')->nullable();
            $table->string('customer_buyer_invoice')->nullable();
            $table->string('box_barcode')->nullable();
            $table->string('box_internal_id');
            $table->string('box_voucher');
            $table->string('box_voucher_new')->nullable();
            $table->integer('box_voucher_status')->default(1);
            $table->string('voucher_activation_date')->nullable();
            $table->string('customer_user_id')->nullable();
            $table->string('redeemed_date')->nullable();
            $table->string('cancellation_date')->nullable();
            $table->string('booking_date')->nullable();
            $table->string('partner_pay_due_date')->nullable();
            $table->string('partner_pay_effec_date')->nullable();
            $table->string('partner_pay_amount')->nullable();
            $table->string('partner_internal_id')->nullable();
            $table->string('partner_invoice')->nullable();
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
        Schema::dropIfExists('inventories');
    }
}
