<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class CheckBookingAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để thực hiện thao tác này.'
            ], 401);
        }

        // Lấy booking ID từ route parameter hoặc request data
        $bookingId = $request->route('bookingId') ?? $request->input('booking_id');
        
        if (!$bookingId) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đặt phòng.'
            ], 400);
        }

        // Tìm booking
        $booking = Booking::find($bookingId);
        
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đặt phòng.'
            ], 404);
        }

        // Kiểm tra quyền truy cập
        $canAccess = false;
        
        // Admin có thể truy cập tất cả
        if ($user->role && $user->role->name === 'admin') {
            $canAccess = true;
        }
        // Staff có thể truy cập tất cả
        elseif ($user->role && $user->role->name === 'staff') {
            $canAccess = true;
        }
        // Customer chỉ có thể truy cập booking của mình
        elseif ($user->role && $user->role->name === 'customer' && $booking->user_id === $user->id) {
            $canAccess = true;
        }

        if (!$canAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập đặt phòng này.'
            ], 403);
        }

        // Thêm booking vào request để sử dụng trong controller
        $request->merge(['booking' => $booking]);
        
        return $next($request);
    }
}
