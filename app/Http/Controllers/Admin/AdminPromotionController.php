<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\AdminPromotionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminPromotionController extends Controller
{
    protected $promotionService;

    public function __construct(AdminPromotionServiceInterface $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    /**
     * Hiển thị danh sách promotion
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['status', 'featured', 'discount_type', 'search']);
            $promotions = $this->promotionService->getPromotions($filters, 15);
            $stats = $this->promotionService->getStats();
            
            return view('admin.promotions.index', compact('promotions', 'stats', 'filters'));
        } catch (\Exception $e) {
            Log::error('Error in AdminPromotionController@index: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Có lỗi xảy ra khi tải danh sách khuyến mại.');
        }
    }

    /**
     * Hiển thị form tạo promotion mới
     */
    public function create()
    {
        // Load room types với số lượng phòng
        $roomTypes = \App\Models\RoomType::withCount('rooms')->get();
            
        return view('admin.promotions.create', compact('roomTypes'));
    }

    /**
     * Lưu promotion mới
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            
            // DEBUG: Log dữ liệu form để kiểm tra
            Log::info('Form data received in AdminPromotionController@store', [
                'apply_scope' => $data['apply_scope'] ?? 'NOT_SET',
                'room_type_ids' => $data['room_type_ids'] ?? 'NOT_SET',
                'all_data' => $data
            ]);
            
            // Service sẽ tự xử lý boolean fields
            // Chỉ pass raw data từ form
            
            $this->promotionService->createPromotion($data);
            
            return redirect()->route('admin.promotions.index')
                ->with('success', 'Tạo khuyến mại thành công!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in store', $e->errors());
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error in AdminPromotionController@store: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tạo khuyến mại: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hiển thị chi tiết promotion
     */
    public function show($id)
    {
        try {
            $promotion = $this->promotionService->getPromotion((int)$id);
            return view('admin.promotions.show', compact('promotion'));
        } catch (\Exception $e) {
            return redirect()->route('admin.promotions.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Hiển thị form chỉnh sửa promotion
     */
    public function edit($id)
    {
        try {
            $promotion = $this->promotionService->getPromotion((int)$id);
            
            // Load room types với số lượng phòng
            $roomTypes = \App\Models\RoomType::withCount('rooms')->get();
                
            return view('admin.promotions.edit', compact('promotion', 'roomTypes'));
            
        } catch (\Exception $e) {
            return redirect()->route('admin.promotions.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Cập nhật promotion
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            
            // Service sẽ tự xử lý boolean fields
            // Chỉ pass raw data từ form
            
            $this->promotionService->updatePromotion((int)$id, $data);
            
            return redirect()->route('admin.promotions.show', $id)
                ->with('success', 'Cập nhật khuyến mại thành công!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error in AdminPromotionController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật khuyến mại: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Xóa promotion
     */
    public function destroy($id)
    {
        $result = $this->promotionService->deletePromotion((int)$id);
        
        return redirect()->route('admin.promotions.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Toggle trạng thái promotion (Ajax)
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $type = $request->input('type'); // 'active' hoặc 'featured'
            $result = $this->promotionService->toggleStatus((int)$id, $type);
            
            if ($request->ajax()) {
                return response()->json($result);
            }
            
            return redirect()->back()
                ->with($result['success'] ? 'success' : 'error', $result['message']);
                
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
            
            if ($request->ajax()) {
                return response()->json($response);
            }
            
            return redirect()->back()->with('error', $response['message']);
        }
    }

    /**
     * Bulk actions (Ajax)
     */
    public function bulkAction(Request $request)
    {
        try {
            $action = $request->input('action');
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn ít nhất một khuyến mại.'
                ]);
            }
            
            $results = [];
            
            foreach ($ids as $id) {
                $promotionId = (int)$id;
                switch ($action) {
                    case 'activate':
                        $results[] = $this->promotionService->toggleStatus($promotionId, 'active');
                        break;
                    case 'deactivate':
                        $results[] = $this->promotionService->toggleStatus($promotionId, 'active');
                        break;
                    case 'feature':
                        $results[] = $this->promotionService->toggleStatus($promotionId, 'featured');
                        break;
                    case 'unfeature':
                        $results[] = $this->promotionService->toggleStatus($promotionId, 'featured');
                        break;
                    case 'delete':
                        $results[] = $this->promotionService->deletePromotion($promotionId);
                        break;
                }
            }
            
            $successCount = count(array_filter($results, function($result) {
                return $result['success'];
            }));
            
            return response()->json([
                'success' => true,
                'message' => "Đã thực hiện thành công {$successCount}/" . count($ids) . " mục."
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
} 