<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $hotel;
    /**
     * Create a new message instance.
     */
    public function __construct($booking)
    {
        $this->booking = $booking->load([
            'guest',
            'hotel',
            'bookingRooms.room.roomType',
        ]);

        $this->hotel = $this->booking->hotel;
    }

    public function build()
    {
        return $this->subject(
            'Your Reservation is Confirmed – ' . $this->hotel->name
        )
            ->view('mail.bookingconfirm');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
