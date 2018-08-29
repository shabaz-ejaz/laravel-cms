<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('active')->default(1);
            $table->string('stripe_id')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card_last_four')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->integer('referred_by')->nullable();
            $table->string('referral_code')->unique()->nullable();
            $table->boolean('approved_referrer')->nullable();
            $table->dateTime('approved_referrer_date')->nullable();
            $table->dateTime('referral_code_redeem_date')->nullable();
            $table->integer('discount_rate')->nullable();
            $table->foreign('discount_rate')->references('id')->on('discount_rates');
            $table->decimal('discount_rate_percentage')->unsigned()->nullable();
            $table->decimal('commission_rate')->default(0.00);
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
