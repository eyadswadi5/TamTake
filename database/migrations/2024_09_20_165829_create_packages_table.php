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
        Schema::create('packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid("shipment_id")->constrained("shipments")->onDelete("cascade");
            
            $table->decimal('weight', 10, 2);
            $table->string('dimensions'); // String to store dimensions like "L * W * H"
            $table->decimal('value', 10, 2);
            $table->enum('package_type', ['box', 'envelope', 'crate', 'pallet']);
            $table->boolean('is_fragile')->default(false);
            $table->boolean('hazardous_materials')->default(false);
            $table->string('barcode')->unique()->nullable();
            $table->text('special_instruction')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
