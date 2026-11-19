<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Paketlər (spec §4, §3, data model §12.1: parcels, parcel_events, parcel_proofs, customers). */
return new class extends Migration
{
    public function up(): void
    {
        // System invoice numbers are sequential (spec §4.2, e.g. 289779).
        DB::statement('CREATE SEQUENCE IF NOT EXISTS parcel_invoice_no_seq START 100001');

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('address', 500)->nullable();
            $table->timestamps();
            $table->unique(['phone', 'name']);
        });

        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id', 30)->unique();
            $table->unsignedBigInteger('invoice_no')->unique();
            $table->string('merchant_invoice_no', 50)->nullable();
            $table->foreignId('merchant_id')->constrained()->restrictOnDelete();
            $table->foreignId('merchant_shop_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hub_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('pickupman_id')->nullable()->constrained('delivery_men')->restrictOnDelete();
            $table->foreignId('delivery_man_id')->nullable()->constrained('delivery_men')->restrictOnDelete();
            $table->foreignId('zone_id')->constrained()->restrictOnDelete();
            $table->foreignId('package_type_id')->constrained()->restrictOnDelete();
            $table->string('tariff_type', 20);
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone', 20)->nullable();
            $table->string('customer_address', 500);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('pickup_address', 500)->nullable();
            $table->text('pickup_point')->nullable();
            $table->unsignedInteger('parcel_count')->default(1);
            $table->decimal('weight', 8, 2);
            $table->boolean('fragile_liquid')->default(false);
            $table->boolean('has_round_trip')->default(false);
            $table->string('priority', 10)->default('normal');
            $table->decimal('cash_collection', 12, 2)->default(0);
            $table->decimal('delivery_charge', 12, 2)->default(0);
            $table->decimal('cod_charge', 12, 2)->default(0);
            $table->decimal('vat', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->unsignedSmallInteger('status')->default(1);
            $table->foreignId('reject_reason_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shelf_id')->nullable()->constrained('shelves')->nullOnDelete();
            $table->unsignedInteger('scanned_count')->default(0);
            $table->unsignedSmallInteger('map_status')->nullable(); // 1 confirmed by the customer, 2 not yet
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_date');
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'status_updated_at']);
            $table->index('created_date');
        });

        Schema::create('parcel_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcel_id')->constrained()->cascadeOnDelete();
            $table->string('action', 40);
            $table->unsignedSmallInteger('from_status')->nullable();
            $table->unsignedSmallInteger('status');
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('delivery_man_id')->nullable()->constrained('delivery_men')->nullOnDelete();
            $table->foreignId('hub_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('from_hub_id')->nullable()->constrained('hubs')->nullOnDelete();
            $table->text('note')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            // previous values of the fields this event changed (used by undo) and action details (payment split, date…)
            $table->jsonb('before')->default('{}');
            $table->jsonb('meta')->default('{}');
            $table->boolean('reverted')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->index(['parcel_id', 'id']);
        });

        Schema::create('parcel_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcel_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->decimal('cash_pay', 12, 2)->default(0);
            $table->decimal('pos_pay', 12, 2)->default(0);
            $table->decimal('card_pay', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcel_proofs');
        Schema::dropIfExists('parcel_events');
        Schema::dropIfExists('parcels');
        Schema::dropIfExists('customers');
        DB::statement('DROP SEQUENCE IF EXISTS parcel_invoice_no_seq');
    }
};
