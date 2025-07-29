<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\BookingServiceInterface;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $bookingService;

    public function __construct(BookingServiceInterface $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function confirmInfo($id)
    {
        try {
            $booking = $this->bookingService->getBookingDetail($id);
            return view('client.booking.confirm-payment', compact('booking'));
        } catch (\Exception $e) {
            return redirect()->route('index')->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function paymentMethod($bookingId)
    {
        $booking = $this->bookingService->getBookingDetail($bookingId);
        return view('client.booking.payment-method', compact('booking'));
    }
}
