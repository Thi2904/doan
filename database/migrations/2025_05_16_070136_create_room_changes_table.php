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
        Schema::create('room_changes', function (Blueprint $table) {
            $table->id();

            // Liên kết đến booking tổng
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();

            // Liên kết đến bản ghi booking_room cũ
            $table->foreignId('booking_room_id')
                ->constrained('booking_rooms')
                ->cascadeOnDelete();

            // Loại phòng trước khi đổi
            $table->foreignId('from_room_type_id')
                ->constrained('room_types')
                ->restrictOnDelete();

            // Loại phòng sau khi đổi
            $table->foreignId('to_room_type_id')
                ->constrained('room_types')
                ->restrictOnDelete();

            // Khoảng thời gian áp dụng đổi room
            $table->date('change_start_date');
            $table->date('change_end_date');

            // Thời điểm thực hiện đổi (có thể = now())
            $table->timestamp('changed_at');

            // Ghi chú lý do đổi, yêu cầu đặc biệt
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_changes');
    }
};
