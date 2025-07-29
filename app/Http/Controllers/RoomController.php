<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\Services\RoomServiceInterface;
use App\Interfaces\Services\RoomTypeServiceInterface;

class RoomController extends Controller
{
    protected $roomService;
    protected $roomTypeService;

    public function __construct(RoomServiceInterface $roomService, RoomTypeServiceInterface $roomTypeService)
    {
        $this->roomService = $roomService;
        $this->roomTypeService = $roomTypeService;
    }

    public function search(Request $request)
    {
        $filters = $request->only(['keyword', 'price_min', 'price_max', 'type']);
        $roomTypes = $this->roomTypeService->searchRoomTypes($filters);
        return view('client.rooms.index', compact('roomTypes'));
    }
}
