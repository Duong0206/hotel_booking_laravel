<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\Repositories\RoomRepositoryInterface;
use App\Models\Coupon;

class HotelController extends Controller
{
    protected $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function index()
    {
        // Lấy 6 phòng ngẫu nhiên để hiển thị ở trang chủ
        $rooms = $this->roomRepository->getAll()->shuffle()->take(6);
        
        // Lấy 3 khuyến mãi nổi bật (còn hiệu lực)
        $coupons = Coupon::active()
                         ->orderBy('discount', 'desc')
                         ->take(3)
                         ->get();
        
        return view('client.index', compact('rooms', 'coupons'));
    }

    public function rooms()
    {
        // Lấy tất cả phòng để hiển thị ở trang danh sách phòng
        $rooms = $this->roomRepository->getAll();
        return view('client.rooms', compact('rooms'));
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
        // Nếu không có id, chuyển hướng đến trang danh sách phòng
        if (!$id) {
            return redirect()->route('rooms');
        }
        
        // Lấy thông tin chi tiết phòng
        $room = $this->roomRepository->findById($id);
        
        if (!$room) {
            return redirect()->route('rooms')->with('error', 'Không tìm thấy phòng');
        }
        
        // Lấy các phòng khác cùng loại
        $relatedRooms = $this->roomRepository->getAll()
            ->where('room_type_id', $room->room_type_id)
            ->where('id', '!=', $room->id)
            ->take(2);
        
        return view('client.rooms-single', compact('room', 'relatedRooms'));
    }

    public function blogSingle()
    {
        return view('client.blog-single');
    }
}
