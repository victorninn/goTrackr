<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('wise_account_name')->nullable()->after('wise_id');
            $table->string('wise_account_no')->nullable()->after('wise_account_name');
            $table->string('wise_routing_no')->nullable()->after('wise_account_no');
            $table->string('wise_swift_bic')->nullable()->after('wise_routing_no');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wise_account_name', 'wise_account_no', 'wise_routing_no', 'wise_swift_bic']);
        });
    }
};
