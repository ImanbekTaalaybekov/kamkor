<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_admin_id')->nullable()->index()->after('id');
            $table->string('access_link_token')->nullable()->after('daysRemaining');
            $table->timestamp('access_link_created_at')->nullable()->after('access_link_token');
            $table->string('kamkor_sync_status')->nullable()->after('access_link_created_at');
            $table->text('kamkor_sync_error')->nullable()->after('kamkor_sync_status');
            $table->timestamp('kamkor_last_synced_at')->nullable()->after('kamkor_sync_error');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['created_by_admin_id']);
            $table->dropColumn([
                'created_by_admin_id',
                'access_link_token',
                'access_link_created_at',
                'kamkor_sync_status',
                'kamkor_sync_error',
                'kamkor_last_synced_at',
            ]);
        });
    }
};
