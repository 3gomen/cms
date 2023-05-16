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
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('category');
            $table->tinyInteger('brand');
            $table->tinyInteger('type');
            $table->string('color');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('guarantee');
            $table->integer('price');
            $table->integer('sell_price');
            $table->boolean('has_storage');
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};
