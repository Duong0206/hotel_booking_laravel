<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\BookingServiceInterface;
use App\Interfaces\Services\PromotionServiceInterface;
use App\Services\RoomPromotionService;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $bookingService;
    protected $promotionService;
    protected $roomPromotionService;

    public function __construct(
        BookingServiceInterface $bookingService,
        PromotionServiceInterface $promotionService,
        RoomPromotionService $roomPromotionService
    ) {
        $this->bookingService = $bookingService;
        $this->promotionService = $promotionService;
        $this->roomPromotionService = $roomPromotionService;
    }

    public function confirmInfo($id)
    {
        try {
            $booking = $this->bookingService->getBookingDetail($id);
            
            // Lấy danh sách khuyến mãi có thể áp dụng cho phòng này
            $promotions = $this->roomPromotionService->getAvailablePromotions($booking->room);
            
            return view('client.booking.confirm-payment', compact('booking', 'promotions'));
        } catch (\Exception $e) {
            return redirect()->route('index')->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function paymentMethod($bookingId)
    {
        try {
            $booking = $this->bookingService->getBookingDetail($bookingId);
            
            // Log để debug
            Log::info('Payment method accessed', [
                'booking_id' => $bookingId,
                'booking_exists' => $booking ? 'yes' : 'no'
            ]);
            
            return view('client.booking.payment-method', compact('booking'));
        } catch (\Exception $e) {
            Log::error('Error in paymentMethod', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('index')->withErrors(['message' => 'Có lỗi xảy ra khi tải trang thanh toán']);
        }
    }

    public function calculatePromotion(Request $request)
    {
        try {
            $request->validate([
                'promotion_id' => 'required|exists:promotions,id',
                'booking_id' => 'required|exists:bookings,id'
            ]);

            $booking = $this->bookingService->getBookingDetail($request->booking_id);
            $promotion = Promotion::findOrFail($request->promotion_id);

            $result = $this->roomPromotionService->calculateWithPromotion($booking->price, $promotion);

            return response()->json([
                'success' => true,
                'data' => [
                    'original_price' => $result['original_price'],
                    'discount_amount' => $result['discount_amount'],
                    'final_price' => $result['final_price'],
                    'formatted' => [
                        'original_price' => number_format($result['original_price'], 0, ',', '.') . ' VNĐ',
                        'discount_amount' => '-' . number_format($result['discount_amount'], 0, ',', '.') . ' VNĐ',
                        'final_price' => number_format($result['final_price'], 0, ',', '.') . ' VNĐ'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
