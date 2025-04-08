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
        Schema::create('je_temps', function (Blueprint $table) {
            $table->id();
            $table->date('create_date')->nullable();
            $table->date('posting_date')->nullable();
            $table->string('tx_num')->nullable();
            $table->string('doc_num')->nullable();
            $table->string('doc_type')->nullable();
            $table->string('project_code')->nullable();
            $table->string('department')->nullable();
            $table->string('account')->nullable();
            $table->string('account_name')->nullable();
            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('credit', 15, 2)->default(0.00);
            $table->decimal('fc_debit', 15, 2)->default(0.00);
            $table->decimal('fc_credit', 15, 2)->default(0.00);
            $table->string('unit_no')->nullable();
            $table->text('remarks')->nullable();
            $table->string('user_code')->nullable();
            $table->string('user_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('je_temps');
    }
};
