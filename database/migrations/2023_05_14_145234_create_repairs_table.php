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
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status');
            $table->timestamp('timestamp');
            $table->foreignId('device_id')->constrained();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('location_id')->constrained('locations');
            $table->timestamp('completion_date')->nullable();
            $table->integer('price');
            $table->text('description');
            $table->text('comment');
            $table->string('passcode')->nullable();
            $table->string('do_datasave');
            $table->string('aesthetics');
            $table->string('system_state');
            $table->text('accessories');
            $table->tinyInteger('service_provider');
            $table->foreignId('replacement_device_id')->nullable()->constrained('devices');
            $table->boolean('is_guarantee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
