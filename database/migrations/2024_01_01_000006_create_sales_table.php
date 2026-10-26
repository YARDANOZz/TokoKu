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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('customer_name')->default('Umum / Walk-in');
            $table->dateTime('sale_date');
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('change_amount', 14, 2)->default(0);
            $table->string('payment_method')->default('cash'); // cash, transfer, qris
            $table->enum('status', ['completed', 'cancelled'])->default('completed');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
