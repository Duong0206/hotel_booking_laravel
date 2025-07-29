<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\ServiceServiceInterface;
use App\Interfaces\Services\ServiceCategoryServiceInterface;
use App\Interfaces\Services\RoomTypeServiceInterface;
use Illuminate\Http\Request;

class AdminServiceController extends Controller
{
    protected $serviceService;
    protected $serviceCategoryService;
    protected $roomTypeService;

    public function __construct(
        ServiceServiceInterface $serviceService, 
        ServiceCategoryServiceInterface $serviceCategoryService,
        RoomTypeServiceInterface $roomTypeService
    ) {
        $this->serviceService = $serviceService;
        $this->serviceCategoryService = $serviceCategoryService;
        $this->roomTypeService = $roomTypeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categoryId = $request->get('category_id');
        $roomTypeId = $request->get('room_type_id');
        $keyword = $request->get('keyword');
        $perPage = 10;
        $services = $this->serviceService->paginateWithFilter($categoryId, $roomTypeId, $perPage, $keyword);
        $categories = $this->serviceCategoryService->getAll();
        $roomTypes = $this->roomTypeService->getAllRoomTypes();
        return view('admin.services.index', compact('services', 'categories', 'roomTypes', 'categoryId', 'roomTypeId', 'keyword'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->serviceCategoryService->getAll();
        $roomTypes = $this->roomTypeService->getAllRoomTypes();
        return view('admin.services.create', compact('categories', 'roomTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'room_type_ids' => 'nullable|array',
            'room_type_ids.*' => 'exists:room_types,id',
        ]);

        // Tách room_type_ids ra khỏi validated data
        $roomTypeIds = $validated['room_type_ids'] ?? [];
        unset($validated['room_type_ids']);

        // Tạo dịch vụ
        $service = $this->serviceService->create($validated);

        // Gán dịch vụ cho các loại phòng được chọn
        if (!empty($roomTypeIds)) {
            $service->roomTypes()->attach($roomTypeIds);
        }

        return redirect()->route('admin.services.index')->with('success', 'Tạo dịch vụ thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $service = $this->serviceService->getById($id);
        $categories = $this->serviceCategoryService->getAll();
        $roomTypes = $this->roomTypeService->getAllRoomTypes();
        $assignedRoomTypeIds = $service->roomTypes->pluck('id')->toArray();
        
        return view('admin.services.edit', compact('service', 'categories', 'roomTypes', 'assignedRoomTypeIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'room_type_ids' => 'nullable|array',
            'room_type_ids.*' => 'exists:room_types,id',
        ]);

        // Tách room_type_ids ra khỏi validated data
        $roomTypeIds = $validated['room_type_ids'] ?? [];
        unset($validated['room_type_ids']);

        // Cập nhật dịch vụ
        $service = $this->serviceService->update($id, $validated);

        // Cập nhật loại phòng được gán
        $service->roomTypes()->sync($roomTypeIds);

        return redirect()->route('admin.services.index')->with('success', 'Cập nhật dịch vụ thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $service = $this->serviceService->getById($id);
        // Xóa liên kết với các loại phòng trước khi xóa service
        if ($service) {
            $service->roomTypes()->detach();
            $service->rooms()->detach(); // Xóa liên kết với các phòng (bảng room_services)
        }
        $this->serviceService->delete($id);
        return redirect()->route('admin.services.index')->with('success', 'Xóa dịch vụ thành công!');
    }
}
