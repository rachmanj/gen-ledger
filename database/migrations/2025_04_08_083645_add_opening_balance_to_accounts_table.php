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
        Schema::table('accounts', function (Blueprint $table) {
            // Add opening balance fields if they don't already exist
            if (!Schema::hasColumn('accounts', 'opening_balance')) {
                $table->decimal('opening_balance', 19, 2)->nullable()->after('parent_account_id');
            }
            
            if (!Schema::hasColumn('accounts', 'opening_balance_date')) {
                $table->date('opening_balance_date')->nullable()->after('opening_balance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Only drop if they exist
            if (Schema::hasColumn('accounts', 'opening_balance')) {
                $table->dropColumn('opening_balance');
            }
            
            if (Schema::hasColumn('accounts', 'opening_balance_date')) {
                $table->dropColumn('opening_balance_date');
            }
        });
    }
};
