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
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('discount_type', ['percentage', 'number'])->after('is_paid');
            $table->decimal('discount_amount', 15, 2)->after('discount_type');
            $table->decimal('total_price_after_discount', 15, 2)->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('discount_type');
            $table->dropColumn('discount_amount');
            $table->dropColumn('total_price_after_discount');
        });
    }
};
