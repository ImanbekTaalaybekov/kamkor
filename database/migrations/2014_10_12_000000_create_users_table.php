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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('pin')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('password')->nullable();
            $table->string('fcm_token')->nullable();
            $table->string('order_registration_date')->nullable();
            $table->string('region')->nullable();
            $table->string('uvd_code')->nullable();
            $table->string('address')->nullable();
            $table->string('icon')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
