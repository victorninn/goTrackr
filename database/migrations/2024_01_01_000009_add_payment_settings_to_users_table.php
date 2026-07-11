<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->default(null); // paypal, wise, bank
            $table->string('paypal_id')->nullable();
            $table->string('wise_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'paypal_id', 'wise_id']);
        });
    }
};
