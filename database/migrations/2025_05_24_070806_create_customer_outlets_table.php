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
        Schema::create('customer_outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('customer_companies')->nullOnDelete();
            $table->string('outlet_id', 10)->index();
            $table->string('outlet_name');
            $table->string('address');
            $table->string('contact', 15);
            $table->enum('type', ['company', 'individual']);
            $table->boolean('kontra_faktur');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_outlets');
    }
};
