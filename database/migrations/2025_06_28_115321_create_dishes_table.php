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
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->string('image')->nullable();
            $table->json('allergens')->nullable(); // ["gluten", "nuts", "dairy"]
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_available')->default(true);
            $table->decimal('rating', 2, 1)->default(0.0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indeksy dla wydajności
            $table->index(['category_id', 'is_available']);
            $table->index(['category_id', 'sort_order']);
            $table->index(['is_vegetarian']);
            $table->index(['is_vegan']);
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};