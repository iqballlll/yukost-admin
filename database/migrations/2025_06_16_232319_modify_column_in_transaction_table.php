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
            $table->date('due_date')->nullable()->change();
            $table->date('payment_date')->nullable()->change();
            $table->boolean('tukar_faktur')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->date('due_date');
            $table->date('payment_date');
            $table->boolean('tukar_faktur');
        });
    }
};
