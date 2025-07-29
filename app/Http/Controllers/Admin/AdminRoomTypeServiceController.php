<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\RoomTypeServiceInterface;
use App\Interfaces\Services\RoomTypeServiceServiceInterface;
use App\Interfaces\Services\ServiceCategoryServiceInterface;
use App\Interfaces\Services\ServiceServiceInterface;
use Illuminate\Http\Request;

class AdminRoomTypeServiceController extends Controller
{
    protected $roomTypeServiceService;
    protected $roomTypeService;
    protected $serviceCategoryService;
    protected $serviceService;

    public function __construct(
        RoomTypeServiceServiceInterface $roomTypeServiceService,
        RoomTypeServiceInterface $roomTypeService,
        ServiceCategoryServiceInterface $serviceCategoryService,
        ServiceServiceInterface $serviceService
    ) {
        $this->roomTypeServiceService = $roomTypeServiceService;
        $this->roomTypeService = $roomTypeService;
        $this->serviceCategoryService = $serviceCategoryService;
        $this->serviceService = $serviceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomTypes = $this->roomTypeService->getAllRoomTypes();
        return view('admin.room-type-services.index', compact('roomTypes'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($roomTypeId)
    {
        $roomType = $this->roomTypeService->findById($roomTypeId);
        $categories = $this->serviceCategoryService->getAll();
        $services = $this->serviceService->getAll();
        $selectedServices = $this->roomTypeServiceService->getServicesByRoomType($roomTypeId)->pluck('service_id')->toArray();
        return view('admin.room-type-services.edit', compact('roomType', 'categories', 'services', 'selectedServices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $roomTypeId)
    {
        $serviceIds = $request->input('service_ids', []);
        $this->roomTypeServiceService->syncServices($roomTypeId, $serviceIds);
        return redirect()->route('admin.room-type-services.index')->with('success', 'Cập nhật dịch vụ cho loại phòng thành công!');
    }
}
