<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Auth;
use App\Services\RoomTypeReviewService;
use App\Interfaces\Services\RoomTypeServiceInterface;
use App\Interfaces\Services\RoomServiceInterface;
use App\Interfaces\Repositories\RoomRepositoryInterface;
use App\Interfaces\Services\PromotionServiceInterface;
use App\Interfaces\Repositories\BookingRepositoryInterface;
use App\Interfaces\Services\ServiceCategoryServiceInterface;

class HotelController extends Controller
{
    protected $roomRepository;
    protected $promotionService;
    protected $roomService;
    protected $reviewService;
    protected $roomTypeReviewService;
    protected $roomTypeService;
    protected $bookingRepository;
    protected $serviceCategoryService;

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        PromotionServiceInterface $promotionService,
        RoomServiceInterface $roomService,
        RoomTypeReviewService $roomTypeReviewService,
        RoomTypeServiceInterface $roomTypeService,
        BookingRepositoryInterface $bookingRepository,
        ServiceCategoryServiceInterface $serviceCategoryService
    ) {
        $this->roomRepository = $roomRepository;
        $this->promotionService = $promotionService;
        $this->roomService = $roomService;
        $this->roomTypeReviewService = $roomTypeReviewService;
        $this->roomTypeService = $roomTypeService;
        $this->bookingRepository = $bookingRepository;
        $this->serviceCategoryService = $serviceCategoryService;
    }

    public function index()
    {
        // Lấy tất cả loại phòng để hiển thị ở trang chủ
        $roomTypes = $this->roomTypeService->getAllRoomTypes()->take(6); // Lấy 6 loại phòng đầu tiên
        
        // Lấy khuyến mại nổi bật
        $featuredPromotions = $this->promotionService->getFeaturedPromotions(3);
        
        return view('client.index', compact('roomTypes', 'featuredPromotions'));
    }

    public function rooms()
    {
        // Lấy tất cả phòng để hiển thị ở trang danh sách phòng
        $rooms = $this->roomRepository->getAll();
        $roomTypes = $this->roomTypeService->getAllRoomTypes();
        return view('client.rooms.index', compact('rooms', 'roomTypes'));
    }

    public function restaurant()
    {
        return view('client.restaurant');
    }

    public function about()
    {
        return view('client.about');
    }

    public function blog()
    {
        return view('client.blog');
    }

    public function contact()
    {
        return view('client.contact');
    }

    public function roomsSingle($id = null)
    {
        // Nếu không có id, chuyển hướng đến trang danh sách loại phòng
        if (!$id) {
            return redirect()->route('rooms');
        }

        // Lấy thông tin chi tiết loại phòng
        $roomType = $this->roomTypeService->findById($id);

        if (!$roomType) {
            return redirect()->route('rooms')->with('error', 'Không tìm thấy loại phòng');
        }

        // Lấy các phòng thuộc loại phòng này
        $rooms = $this->roomService->getAll()
            ->where('room_type_id', $roomType->id);

        // Lấy đánh giá và bình luận của loại phòng
        $reviews = $this->roomTypeReviewService->getRoomTypeReviews($roomType->id, 10);
        $averageRating = $this->roomTypeReviewService->getRoomTypeAverageRating($roomType->id);
        $reviewsCount = $this->roomTypeReviewService->getRoomTypeReviewsCount($roomType->id);

        // Kiểm tra quyền đánh giá
        $canReview = false;
        $completedBookings = collect();
        if (Auth::check()) {
            // Lấy tất cả booking hoàn thành cho loại phòng này mà chưa đánh giá
            $completedBookings = \App\Models\Booking::where('user_id', Auth::id())
                ->where('status', 'completed')
                ->whereHas('room', function($query) use ($roomType) {
                    $query->where('room_type_id', $roomType->id);
                })
                ->whereDoesntHave('review', function($q) {
                    $q->where('user_id', Auth::id());
                })
                ->get();

            $canReview = $completedBookings->isNotEmpty();
        }

        $roomTypes = $this->roomTypeService->getAllRoomTypes();
        $serviceCategories = $this->serviceCategoryService->getAll();
        return view('client.rooms.single', compact('roomType', 'rooms', 'reviews', 'averageRating', 'reviewsCount', 'roomTypes', 'canReview', 'completedBookings', 'serviceCategories'));
    }

    public function blogSingle()
    {
        return view('client.blog-single');
    }

    /**
     * Lấy danh sách reviews của loại phòng qua AJAX
     */
    public function getRoomReviewsAjax($id)
    {
        $roomType = $this->roomTypeService->findById($id);

        if (!$roomType) {
            return response()->json(['error' => 'Không tìm thấy loại phòng'], 404);
        }

        $reviews = $this->roomTypeReviewService->getRoomTypeReviews($roomType->id, 10);

        return view('client.rooms.reviews-list', compact('reviews'))->render();
    }
}