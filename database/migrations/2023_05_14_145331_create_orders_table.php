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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();
            $table->tinyInteger('category');
            $table->text('description');
            $table->tinyInteger('status');
            $table->timestamp('arriving_date');
            $table->foreignId('location_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->integer('price');
            $table->integer('sell_price');
            $table->timestamp('timestamp');
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
