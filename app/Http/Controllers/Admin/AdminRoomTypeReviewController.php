<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\RoomTypeReviewServiceInterface;
use App\Interfaces\Services\RoomTypeServiceInterface;
use App\Interfaces\Services\UserServiceInterface;
use Illuminate\Http\Request;

class AdminRoomTypeReviewController extends Controller
{
    protected $roomTypeReviewService;
    protected $roomTypeService;
    protected $userService;

    public function __construct(
        RoomTypeReviewServiceInterface $roomTypeReviewService,
        RoomTypeServiceInterface $roomTypeService,
        UserServiceInterface $userService
    ) {
        $this->roomTypeReviewService = $roomTypeReviewService;
        $this->roomTypeService = $roomTypeService;
        $this->userService = $userService;
    }

    /**
     * Hiển thị danh sách tất cả reviews
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'room_type_id', 'rating', 'user_id', 'search']);
        $reviews = $this->roomTypeReviewService->getAllReviewsForAdmin($filters, 15);
        $roomTypes = $this->roomTypeService->getAllRoomTypes();

        return view('admin.room-type-reviews.index', compact('reviews', 'roomTypes', 'filters'));
    }

    /**
     * Hiển thị form tạo review mới
     */
    public function create()
    {
        $roomTypes = $this->roomTypeService->getAllRoomTypes();
        $users = $this->userService->getAll();
        return view('admin.room-type-reviews.create', compact('roomTypes', 'users'));
    }

    /**
     * Lưu review mới
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $this->roomTypeReviewService->validateReviewData($request->all());
            
            // Xử lý checkbox is_anonymous
            $validatedData['is_anonymous'] = isset($validatedData['is_anonymous']) && $validatedData['is_anonymous'] == '1';
            
            $this->roomTypeReviewService->createReview($validatedData);

            return redirect()->route('admin.room-type-reviews.index')
                ->with('success', 'Review đã được tạo thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết review
     */
    public function show($id)
    {
        $review = $this->roomTypeReviewService->getReviewById($id);
        
        if (!$review) {
            return redirect()->route('admin.room-type-reviews.index')
                ->with('error', 'Không tìm thấy review.');
        }

        return view('admin.room-type-reviews.show', compact('review'));
    }

    /**
     * Duyệt review
     */
    public function approve($id)
    {
        try {
            $this->roomTypeReviewService->approveReview($id);
            return redirect()->back()->with('success', 'Review đã được duyệt thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Từ chối review
     */
    public function reject($id)
    {
        try {
            $this->roomTypeReviewService->rejectReview($id);
            return redirect()->back()->with('success', 'Review đã được từ chối.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Xóa review
     */
    public function destroy($id)
    {
        try {
            $this->roomTypeReviewService->deleteReviewForAdmin($id);
            return redirect()->route('admin.room-type-reviews.index')
                ->with('success', 'Review đã được xóa thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Hiển thị thống kê reviews
     */
    public function statistics()
    {
        $statistics = $this->roomTypeReviewService->getReviewStatistics();
        return view('admin.room-type-reviews.statistics', compact('statistics'));
    }
} 