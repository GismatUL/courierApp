<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * İstifadəçilər və Rollar (spec §9): a role is a permission template; each user keeps an own
 * permission set (copied from the role and editable), plus position, department and visible hubs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->jsonb('permissions')->default('[]');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        foreach (['designations', 'departments'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('title', 100)->unique();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained()->restrictOnDelete();
            $table->foreignId('designation_id')->nullable()->after('role_id')->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('designation_id')->constrained()->nullOnDelete();
            $table->jsonb('permissions')->default('[]')->after('password');
            $table->string('address', 500)->nullable()->after('mobile');
            $table->string('nid_number', 30)->nullable()->after('address');
            $table->date('joining_date')->nullable()->after('nid_number');
            $table->decimal('salary', 12, 2)->default(0)->after('joining_date');
        });

        Schema::create('hub_user', function (Blueprint $table) {
            $table->foreignId('hub_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['hub_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hub_user');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropConstrainedForeignId('designation_id');
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['permissions', 'address', 'nid_number', 'joining_date', 'salary']);
        });

        Schema::dropIfExists('departments');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('roles');
    }
};
