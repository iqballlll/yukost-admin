<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->date('order_date')->nullable()->change();
            $table->date('due_date')->nullable()->change();
            $table->date('payment_date')->nullable()->change();
            $table->decimal('total_price', 15, 2)->nullable()->change();
            $table->boolean('is_paid')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->date('order_date')->change();
            $table->date('due_date')->change();
            $table->date('payment_date')->change();
            $table->decimal('total_price', 15, 2)->change();
            $table->boolean('is_paid')->change();
        });
    }
};
