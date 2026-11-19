<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Partnyor idarəetməsi (spec §6, data model §12.1). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_types', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->unique();
            $table->timestamps();
        });

        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->char('unique_id', 6)->unique();
            $table->foreignId('merchant_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('business_name');
            // Partner portal login (the portal itself is out of scope, spec §13.4); password is optional.
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('mobile', 20)->nullable();
            $table->string('password')->nullable();
            $table->text('address')->nullable();
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->decimal('vat', 5, 2)->default(0);
            $table->decimal('return_charge_pct', 5, 2)->default(0);
            $table->decimal('cod_charge_inside_city', 5, 2)->default(1);
            $table->decimal('cod_charge_sub_city', 5, 2)->default(2);
            $table->decimal('cod_charge_outside_city', 5, 2)->default(3);
            $table->unsignedSmallInteger('payment_period_days')->nullable();
            $table->boolean('wallet_enabled')->default(false);
            $table->boolean('otp_required')->default(false);
            $table->string('reference_name')->nullable();
            $table->string('reference_phone', 20)->nullable();
            $table->string('nid_file')->nullable();
            $table->string('trade_license_file')->nullable();
            $table->string('image_file')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('hub_merchant', function (Blueprint $table) {
            $table->foreignId('hub_id')->constrained()->restrictOnDelete();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->primary(['hub_id', 'merchant_id']);
        });

        Schema::create('merchant_shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('contact_no', 20)->nullable();
            $table->string('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        // At most one default pickup point per merchant.
        DB::statement('CREATE UNIQUE INDEX merchant_shops_one_default ON merchant_shops (merchant_id) WHERE is_default');

        Schema::create('merchant_delivery_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained()->restrictOnDelete();
            $table->foreignId('package_type_id')->constrained()->restrictOnDelete();
            $table->string('tariff_type', 20);
            $table->string('tracking_time', 50)->nullable();
            $table->string('delivery_time', 50)->nullable();
            $table->decimal('price', 12, 2);
            $table->timestamps();
            $table->unique(['merchant_id', 'zone_id', 'package_type_id', 'tariff_type'], 'merchant_delivery_charges_unique');
        });

        Schema::create('merchant_payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('method', 10);
            $table->jsonb('details');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_payment_accounts');
        Schema::dropIfExists('merchant_delivery_charges');
        Schema::dropIfExists('merchant_shops');
        Schema::dropIfExists('hub_merchant');
        Schema::dropIfExists('merchants');
        Schema::dropIfExists('merchant_types');
    }
};
