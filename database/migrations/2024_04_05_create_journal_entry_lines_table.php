<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained();
            $table->text('description')->nullable(); // remarks
            $table->string('unit_no')->nullable();
            $table->string('project_code')->nullable();
            $table->string('department_name')->nullable();
            $table->decimal('debit_amount', 15, 2)->default(0.00);
            $table->decimal('credit_amount', 15, 2)->default(0.00);
            $table->decimal('fc_debit_amount', 15, 2)->default(0.00);
            $table->decimal('fc_credit_amount', 15, 2)->default(0.00);
            $table->timestamps();

            $table->index(['journal_entry_id', 'account_id']);
        });

        // Add check constraints using raw SQL
        DB::statement('ALTER TABLE journal_entry_lines ADD CONSTRAINT check_debit_non_negative CHECK (debit_amount >= 0)');
        DB::statement('ALTER TABLE journal_entry_lines ADD CONSTRAINT check_credit_non_negative CHECK (credit_amount >= 0)');
        DB::statement('ALTER TABLE journal_entry_lines ADD CONSTRAINT check_debit_credit_exclusive CHECK ((debit_amount = 0 AND credit_amount > 0) OR (debit_amount > 0 AND credit_amount = 0))');
    }

    public function down()
    {
        Schema::dropIfExists('journal_entry_lines');
    }
}; 