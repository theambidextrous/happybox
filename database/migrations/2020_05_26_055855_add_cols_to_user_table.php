<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColsToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 50)->change();
            $table->renameColumn('name', 'username');
            $table->boolean('is_admin')->nullable();
            $table->boolean('is_client')->nullable();
            $table->boolean('is_partner')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->renameColumn('username', 'name');
            $table->dropColumn('is_admin');
            $table->dropColumn('is_client');
            $table->dropColumn('is_partner');
        });
    }
}
