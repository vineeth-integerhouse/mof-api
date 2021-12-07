<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('payer')->unsigned();
            $table->bigInteger('payee')->unsigned();
         
            $table->bigInteger('card_detail_id')->unsigned();
            $table->foreign('card_detail_id')->references('id')->on('card_details')->onDelete('cascade');

            $table->string('name')->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('amount')->nullable();
            $table->string('status')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('stripe_reference_number')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('payments');
    }
}
