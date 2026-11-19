<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** İdarəetmə reference data (spec §7.1, §7.3, §7.4, §7.6, §7.7). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hubs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->foreignId('contact_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('package_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('min_weight', 8, 2)->default(0);
            $table->decimal('max_weight', 8, 2)->nullable(); // null = no upper limit ("Bağlama50+")
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('hub_id')->nullable()->constrained()->restrictOnDelete();
            $table->text('note')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->unique(['hub_id', 'name']);
        });

        Schema::create('zone_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_type_id')->constrained()->restrictOnDelete();
            $table->string('tariff_type', 20);
            $table->string('tracking_time', 50)->nullable();
            $table->string('delivery_time', 50)->nullable();
            $table->decimal('price', 12, 2);
            $table->timestamps();
            $table->unique(['zone_id', 'package_type_id', 'tariff_type']);
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->unique(['zone_id', 'name']);
        });

        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 30)->unique();
            $table->foreignId('zone_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('limit')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('reject_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reject_reasons');
        Schema::dropIfExists('shelves');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('zone_tariffs');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('package_types');
        Schema::dropIfExists('hubs');
    }
};
