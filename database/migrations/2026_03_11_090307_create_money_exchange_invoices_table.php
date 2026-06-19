<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('money_exchange_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('phone')->nullable();
            
            $table->string('exchange_rate_id')->nullable();
            $table->string('from_currency')->nullable();
            $table->string('to_currency')->nullable();
            $table->string('exchange_type')->nullable();
            $table->string('exchange_rate')->nullable();

            $table->string('where_to_send')->nullable();
            $table->string('entered_amount')->nullable();
            $table->string('subtotal')->nullable();
            $table->string('service_fee')->nullable();
            $table->string('final_amount')->nullable();
            $table->string('receive_type')->nullable();   // cash / bank
            $table->string('invoice_slip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_exchange_invoices');
    }
};
