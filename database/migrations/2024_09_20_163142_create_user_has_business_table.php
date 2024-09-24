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
        Schema::create('user_has_business', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('manager_id')->constrained('users')->onDelete('cascade');
            $table->string('business_name');
            $table->string('business_registration_number')->nullable();
            // $table->string('phone_number');
            // $table->string('primary_contact_person')->nullable();
            // $table->string('email');
            $table->string('website')->nullable();
            $table->enum('industry_type', ['retail', 'manufacturing', 'service', 'other'])->nullable();
            // $table->enum('shipping_volume', ['low', 'mid', 'high'])->default('low');
            // $table->enum('preferred_shipping_method', ['standard', 'express', 'overnight'])->default('standard');
            $table->enum('account_status', ['suspended', 'active', 'closed'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_has_business');
    }
};
