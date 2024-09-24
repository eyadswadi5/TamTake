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
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('customer_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignUuid('origin_address_id')->constrained('addresses')->onDelete('cascade'); 
            $table->foreignUuid('destination_address_id')->constrained('destinations')->onDelete('cascade');
            $table->timestamp('shipment_date');
            $table->timestamp('delivery_date')->nullable();
            $table->enum('status', ['pending', 'in transit', 'delivered', 'canceled'])->default('pending');
            $table->enum('shipping_method', ['standard', 'express', 'overnight'])->default('standard');
            $table->foreignUuid('courier_id')->constrained('users')->onDelete('cascade');
            $table->string('tracking_number')->unique();
            $table->decimal('total_weight', 8, 2)->nullable();
            $table->decimal('total_value', 10, 2)->nullable();
            $table->decimal('insurance', 10, 2)->nullable();
            $table->string('special_instructions')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
