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
        Schema::create('money_transfer_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('transfer_type')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('acc_name')->nullable();
            $table->string('acc_number')->nullable();
            $table->string('currency')->nullable();
            $table->string('entered_amount')->nullable();
            $table->string('trf_fee_in_persentage')->nullable();
            $table->string('trf_fee')->nullable();
            $table->string('net_amount')->nullable();
            $table->enum('status', ['pending_bkk_approval', 'accepted_bkk', 'completed', 'Rejected', 'cancelled'])->default('pending_bkk_approval');
            $table->string('reject_reason')->nullable();
            $table->string('invoice_url')->nullable();
            $table->string('invoice_slip')->nullable();
            $table->string('transaction_slip')->nullable();
            $table->string('createdBy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_transfer_invoices');
    }
};
