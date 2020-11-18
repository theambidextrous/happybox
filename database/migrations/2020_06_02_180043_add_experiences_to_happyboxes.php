<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExperiencesToHappyboxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('happyboxes', function (Blueprint $table) {
            $table->string('partners');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('happyboxes', function (Blueprint $table) {
            $table->dropColumn('voucher');
            $table->dropColumn('stock');
        });
    }
}
