<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCreatedGuest extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        $this->booking->load(['bookingRooms.roomType', 'bookingRooms.surcharges']);

        return $this->view('emails.booking.guest_created') // hoặc blade tuỳ tên bạn đặt
        ->subject("Xác nhận đặt phòng #{$this->booking->booking_code}");
    }
}
