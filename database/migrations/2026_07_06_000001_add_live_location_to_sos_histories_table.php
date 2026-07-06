<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sos_histories', function (Blueprint $table) {
            $table->decimal('location_accuracy', 10, 2)->nullable()->after('geo');
            $table->timestamp('last_location_at')->nullable()->after('location_accuracy');
        });
    }

    public function down(): void
    {
        Schema::table('sos_histories', function (Blueprint $table) {
            $table->dropColumn(['location_accuracy', 'last_location_at']);
        });
    }
};
