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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('imei');
            $table->tinyInteger('brand');
            $table->tinyInteger('type');
            $table->string('storage');
            $table->tinyInteger('status');
            $table->foreignId('location_id')->constrained('locations');
            $table->string('guarantee');
            $table->boolean('is_used');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->text('accessories');
            $table->text('issues');
            $table->tinyInteger('service_provider');
            $table->string('aesthetics');
            $table->string('system_state');
            $table->text('comment');
            $table->integer('price');
            $table->integer('sell_price');
            $table->integer('vat');
            $table->timestamp('timestamp');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('is_tested');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
