<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\ServiceCategoryServiceInterface;
use Illuminate\Http\Request;

class AdminServiceCategoryController extends Controller
{
    protected $serviceCategoryService;

    public function __construct(ServiceCategoryServiceInterface $serviceCategoryService)
    {
        $this->serviceCategoryService = $serviceCategoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->serviceCategoryService->getAll();
        return view('admin.service-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.service-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name',
        ]);
        $this->serviceCategoryService->create($validated);
        return redirect()->route('admin.service-categories.index')->with('success', 'Tạo danh mục thành công!');
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = $this->serviceCategoryService->getById($id);
        return view('admin.service-categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name,' . $id,
        ]);
        $this->serviceCategoryService->update($id, $validated);
        return redirect()->route('admin.service-categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->serviceCategoryService->delete($id);
        return redirect()->route('admin.service-categories.index')->with('success', 'Xóa danh mục thành công!');
    }
}
