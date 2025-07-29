<?php

namespace App\Http\Controllers;

use App\Services\RoomTypeReviewService;
use App\Interfaces\Services\RoomTypeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoomTypeReviewController extends Controller
{
    protected $roomTypeReviewService;
    protected $roomTypeService;

    public function __construct(
        RoomTypeReviewService $roomTypeReviewService,
        RoomTypeServiceInterface $roomTypeService
    ) {
        $this->roomTypeReviewService = $roomTypeReviewService;
        $this->roomTypeService = $roomTypeService;
    }

    /**
     * Hiển thị danh sách loại phòng có thể đánh giá
     */
    public function index()
    {
        if (!Auth::check()) {
            return view('client.room-type-reviews.index', ['roomTypes' => collect()]);
        }
        
        $roomTypes = $this->roomTypeReviewService->getReviewableRoomTypes();
        
        return view('client.room-type-reviews.index', compact('roomTypes'));
    }

    /**
     * Hiển thị form tạo review cho loại phòng
     */
    public function create($roomTypeId)
    {
        // Kiểm tra xem loại phòng có thể đánh giá không
        if (!$this->roomTypeReviewService->canReviewRoomType((int)$roomTypeId)) {
            return redirect()->route('room-type-reviews.index')
                ->with('error', 'Bạn không thể đánh giá loại phòng này hoặc đã đánh giá rồi.');
        }

        $roomType = $this->roomTypeService->findById($roomTypeId);
        
        if (!$roomType) {
            return redirect()->route('room-type-reviews.index')
                ->with('error', 'Không tìm thấy loại phòng.');
        }

        return view('client.room-type-reviews.create', compact('roomType'));
    }

    /**
     * Lưu review mới cho loại phòng
     */
    public function store(Request $request, $roomTypeId)
    {
        try {
            // Kiểm tra xem loại phòng có thể đánh giá không
            if (!$this->roomTypeReviewService->canReviewRoomType((int)$roomTypeId)) {
                return redirect()->route('room-type-reviews.index')
                    ->with('error', 'Bạn không thể đánh giá loại phòng này hoặc đã đánh giá rồi.');
            }

            // Debug: Log request data
            Log::info('Room Type Review store request:', [
                'room_type_id' => $roomTypeId,
                'data' => $request->all()
            ]);
            
            $validatedData = $this->roomTypeReviewService->validateReviewData($request->all());
            
            // Debug: Log validated data
            Log::info('Validated data:', $validatedData);
            
            $this->roomTypeReviewService->createReviewForUser($validatedData, (int)$roomTypeId);

            return redirect()->route('room-type-reviews.index')
                ->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt.');
        } catch (\Exception $e) {
            // Debug: Log error
            Log::error('Room Type Review store error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Hiển thị form chỉnh sửa review
     */
    public function edit($id)
    {
        $review = $this->roomTypeReviewService->getReviewById($id);
        
        if (!$review || $review->user_id !== Auth::id()) {
            return redirect()->route('room-type-reviews.index')
                ->with('error', 'Không tìm thấy review hoặc không có quyền chỉnh sửa.');
        }

        if (!$this->roomTypeReviewService->canBeEdited($review)) {
            return redirect()->route('room-type-reviews.index')
                ->with('error', 'Không thể chỉnh sửa đánh giá này.');
        }

        return view('client.room-type-reviews.edit', compact('review'));
    }

    /**
     * Cập nhật review
     */
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $this->roomTypeReviewService->validateReviewData($request->all());
            $this->roomTypeReviewService->updateReview($id, $validatedData);

            return redirect()->route('room-type-reviews.index')
                ->with('success', 'Đánh giá đã được cập nhật thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Xóa review
     */
    public function destroy($id)
    {
        try {
            $this->roomTypeReviewService->deleteReview($id);

            return redirect()->route('room-type-reviews.index')
                ->with('success', 'Đánh giá đã được xóa thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Hiển thị reviews của một loại phòng
     */
    public function roomTypeReviews($roomTypeId)
    {
        $roomType = $this->roomTypeService->findById($roomTypeId);
        
        if (!$roomType) {
            return redirect()->route('room-type-reviews.index')
                ->with('error', 'Không tìm thấy loại phòng.');
        }
        
        $reviews = $this->roomTypeReviewService->getRoomTypeReviews($roomTypeId, 10);

        return view('client.room-type-reviews.room-type-reviews', compact('roomType', 'reviews'));
    }

    /**
     * Hiển thị tất cả reviews của user
     */
    public function myReviews()
    {
        $reviews = $this->roomTypeReviewService->getUserReviews(10);
        return view('client.room-type-reviews.my-reviews', compact('reviews'));
    }

    /**
     * Hiển thị chi tiết review cho người dùng
     */
    public function show($id)
    {
        $review = $this->roomTypeReviewService->getReviewById($id);
        // Kiểm tra quyền truy cập: chỉ cho phép user xem review của chính mình
        if (!$review || $review->user_id !== Auth::id()) {
            return redirect()->route('room-type-reviews.index')
                ->with('error', 'Không tìm thấy review hoặc không có quyền truy cập.');
        }

        return view('client.room-type-reviews.show', compact('review'));
    }
    
    /**
     * Lưu review qua AJAX
     */
    public function storeAjax(Request $request)
    {
        try {
            $roomTypeId = $request->input('room_type_id');
            $bookingId = $request->input('booking_id');
            

            
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để đánh giá.'
                ], 401);
            }
            
            if (!is_numeric($roomTypeId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID loại phòng không hợp lệ.'
                ], 400);
            }
            
            if (!is_numeric($bookingId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID booking không hợp lệ.'
                ], 400);
            }
            
            // Kiểm tra booking có tồn tại không
            $booking = \App\Models\Booking::find($bookingId);
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking không tồn tại.'
                ], 400);
            }
            
            // Kiểm tra booking có thuộc về user hiện tại không
            if ($booking->user_id != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking không thuộc về bạn.'
                ], 400);
            }
            
            // Kiểm tra booking có status completed không
            if ($booking->status != 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể đánh giá booking đã hoàn thành.'
                ], 400);
            }
            
            if (!$this->roomTypeReviewService->canReviewBooking((int)$bookingId, (int)$roomTypeId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể đánh giá booking này hoặc đã đánh giá rồi.'
                ], 400);
            }
            
            $validatedData = $this->roomTypeReviewService->validateReviewData($request->all());
            
            $this->roomTypeReviewService->createReviewForUser($validatedData, (int)$roomTypeId);
            
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá đã được gửi thành công và hiển thị ngay!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Lấy danh sách reviews của loại phòng qua AJAX
     */
    public function getReviewsAjax($roomTypeId)
    {
        try {
            $roomType = $this->roomTypeService->findById($roomTypeId);
            
            if (!$roomType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy loại phòng.'
                ], 404);
            }
            
            $reviews = $this->roomTypeReviewService->getRoomTypeReviews($roomTypeId, 10);
            
            return view('client.reviews.list', compact('reviews', 'roomType'))->render();
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách đánh giá.'
            ], 500);
        }
    }

    /**
     * Trả về form đánh giá cho popup/modal
     */
    public function reviewForm($roomTypeId, Request $request)
    {
        try {
            Log::info('Review form requested for room type: ' . $roomTypeId);
            $roomType = \App\Models\RoomType::findOrFail($roomTypeId);
            $booking = null;
            $bookingId = $request->get('booking_id');
            if ($bookingId) {
                $booking = \App\Models\Booking::find($bookingId);
            }
            Log::info('Room type found: ' . $roomType->name);
            return view('client.reviews.form', compact('roomType', 'booking'));
        } catch (\Exception $e) {
            Log::error('Review form error: ' . $e->getMessage());
            return response()->json(['error' => 'Không thể tải form đánh giá: ' . $e->getMessage()], 500);
        }
    }
} 