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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('restaurant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('dish_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('rating'); // 1-5 gwiazdek
            $table->text('comment');
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indeksy dla wydajności
            $table->index(['restaurant_id', 'is_approved']);
            $table->index(['dish_id', 'is_approved']);
            $table->index(['user_id']);
            $table->index(['rating']);
            
            // Użytkownik może dodać tylko jedną opinię na danie lub restaurację
            $table->unique(['user_id', 'restaurant_id'], 'unique_user_restaurant_review');
            $table->unique(['user_id', 'dish_id'], 'unique_user_dish_review');
            
            // Uwaga: ograniczenie że opinia musi być o restauracji LUB o daniu będzie w walidacji
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};