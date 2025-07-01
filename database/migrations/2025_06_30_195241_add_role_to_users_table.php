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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'manager', 'staff', 'customer'])->default('customer')->after('email');
            $table->foreignId('restaurant_id')->nullable()->constrained()->onDelete('set null')->after('role');
            
            // Indeks dla wydajności
            $table->index(['role']);
            $table->index(['restaurant_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropIndex(['role']);
            $table->dropIndex(['restaurant_id', 'role']);
            $table->dropColumn(['role', 'restaurant_id']);
        });
    }
};