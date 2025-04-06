<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number')->unique();
            $table->string('name');
            $table->foreignId('account_type_id')->nullable()->constrained('account_types');
            $table->enum('normal_balance', ['debit', 'credit'])->nullable();
            $table->text('description')->nullable();
            $table->foreignId('parent_account_id')->nullable()->constrained('accounts');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}; 