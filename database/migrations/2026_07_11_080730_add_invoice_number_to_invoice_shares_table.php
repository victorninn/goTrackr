<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_shares', function (Blueprint $table) {
            // Sequential per-employee invoice number: 1, 2, 3... displayed as 0001, 0002, ...
            $table->unsignedInteger('invoice_number')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_shares', function (Blueprint $table) {
            $table->dropColumn('invoice_number');
        });
    }
};
