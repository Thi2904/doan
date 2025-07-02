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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            // Quan hệ tới users
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('booking_code', 20)
                ->unique();

            // Thời gian đặt phòng
            $table->date('check_in');
            $table->date('check_out');
            $table->dateTime('actual_check_in')->nullable();
            $table->dateTime('actual_check_out')->nullable();

            // Thông tin khách
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone', 50);
            $table->string('guest_id_number', 20)->nullable();

            // Thống kê khách
            $table->unsignedTinyInteger('num_adults')->default(0);
            $table->unsignedTinyInteger('num_children')->default(0);

            // Tổng giá (phòng + phí phụ)
            $table->decimal('total_price', 12, 2)->default(0.00);

            // Quan hệ tới phương thức thanh toán mặc định (nếu có)
            $table->foreignId('payment_method_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Trạng thái booking
            $table->enum('status', [
                'pending',    // chờ admin duyệt
                'confirmed',  // đã confirm
                'checked_in',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->text('cancel_reason')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
