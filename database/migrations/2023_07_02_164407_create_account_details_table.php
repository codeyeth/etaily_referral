<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountDetailsTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('account_details', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('referral_code');
            $table->integer('account_balance');
            $table->boolean('have_pending_bonus')->nullable()->default(false);
            $table->boolean('is_referred')->nullable()->default(false);
            $table->integer('referred_by_id')->nullable();
            $table->string('referred_by_name')->nullable();
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
        Schema::dropIfExists('account_details');
    }
}
