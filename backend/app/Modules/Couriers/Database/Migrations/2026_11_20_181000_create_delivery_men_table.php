<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kuryerlər (spec §7.2, data model §12.1 delivery_men). The login account is a user (app-only);
 * this row holds the courier profile, fees and the balance kept separately per payment channel.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_men', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('hub_id')->constrained()->restrictOnDelete();
            $table->string('type', 20);
            $table->string('car_type', 20);
            $table->string('car_number', 20)->nullable();
            $table->string('voen', 20)->nullable();
            $table->unsignedInteger('min_delivery_parcel')->default(0);
            $table->unsignedInteger('min_pickup_parcel')->default(0);
            $table->decimal('delivery_charge', 12, 2)->default(0);
            $table->decimal('pickup_charge', 12, 2)->default(0);
            $table->decimal('return_charge', 12, 2)->default(0);
            $table->string('bonus_type', 20)->nullable();
            $table->decimal('salary', 12, 2)->default(0);
            $table->unsignedSmallInteger('work_days')->nullable();
            $table->decimal('opening_balance', 12, 2)->default(0);
            // Money the courier holds, per channel (spec §5.1, §7.2): cash, POS terminal, card-to-card.
            $table->decimal('balance_cash', 12, 2)->default(0);
            $table->decimal('balance_pos', 12, 2)->default(0);
            $table->decimal('balance_card', 12, 2)->default(0);
            $table->string('device_id')->nullable();
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('image_file')->nullable();
            $table->string('driving_license_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_men');
    }
};
