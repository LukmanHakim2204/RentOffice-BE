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
        Schema::create('booking_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('phone_number');
            $table->string('booking_trx_id');
            $table->boolean('is_paid');
            $table->date('ended_at');
            $table->foreignId('office_space_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('duration');
            $table->date('started_at');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_transactions');
    }
};
