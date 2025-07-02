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
        Schema::create('booking_room_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_room_id')->constrained('booking_rooms')->cascadeOnDelete();
            $table->foreignId('fee_id')->constrained('additional_fees')->restrictOnDelete();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_room_fees');
    }
};
