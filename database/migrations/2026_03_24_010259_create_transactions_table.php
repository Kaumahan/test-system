<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('business');
            $table->string('category');
            $table->string('transaction_type');
            $table->string('source');
            $table->string('status');
            $table->timestamps();

            // Optional: Index for filtering performance
            $table->index([ 'business', 'category', 'status' ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
