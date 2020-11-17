<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserinfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userinfos', function (Blueprint $table) {
            $table->id();
            $table->string('internal_id')->unique();
            $table->string('fname');
            $table->string('mname');
            $table->string('sname');
            $table->string('short_description')->nullable();
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->string('picture')->default('default.jpg');
            $table->string('userid')->unique();
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
        Schema::dropIfExists('userinfos');
    }
}
