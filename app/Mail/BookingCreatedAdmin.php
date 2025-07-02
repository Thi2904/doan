<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCreatedAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this
            ->subject("[Admin] Có booking mới: #{$this->booking->booking_code}")
            ->view('emails.booking.admin_created') // Dùng view Blade thường
            ->with([
                'booking' => $this->booking,
            ]);
    }
}
