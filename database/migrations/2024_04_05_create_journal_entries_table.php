<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date'); // created date
            $table->date('posting_date'); // posting date
            $table->string('tx_num')->nullable(); //tx_num
            $table->string('doc_num')->nullable(); //doc_num
            $table->string('doc_type')->nullable(); //doc_type
            $table->string('description')->nullable(); // remarks
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('posted');
            $table->string('sap_user')->nullable(); //user code
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->integer('batch_number')->nullable();
            $table->timestamps();

            $table->index('entry_date');
            $table->index('posting_date');
            $table->index('tx_num');
            $table->index('doc_num');
        });
    }

    public function down()
    {
        Schema::dropIfExists('journal_entries');
    }
}; 