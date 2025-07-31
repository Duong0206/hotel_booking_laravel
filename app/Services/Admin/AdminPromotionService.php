<?php

namespace App\Services\Admin;

use App\Models\Promotion;
use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminPromotionService implements \App\Interfaces\Services\Admin\AdminPromotionServiceInterface
{
    /**
     * Lấy danh sách khuyến mại có phân trang và lọc
     */
    public function getPromotions(array $filters = [], int $perPage = 15)
    {
        $query = Promotion::query();
        
        // Filter by status
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Filter by featured
        if (isset($filters['featured'])) {
            $query->where('is_featured', filter_var($filters['featured'], FILTER_VALIDATE_BOOLEAN));
        }
        
        // Filter by discount type
        if (isset($filters['discount_type'])) {
            $query->where('discount_type', $filters['discount_type']);
        }
        
        // Search by title, code, description
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('code', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }
        
        return $query->latest()->paginate($perPage);
    }

    /**
     * Lấy thống kê tổng quan
     */
    public function getStats(): array
    {
        return [
            'total' => Promotion::count(),
            'active' => Promotion::active()->count(),
            'featured' => Promotion::featured()->count(),
            'expired' => Promotion::where('expired_at', '<', now())->count()
        ];
    }

    /**
     * Lấy chi tiết khuyến mại
     */
    public function getPromotion(int $id): Promotion
    {
        $promotion = Promotion::with(['roomTypes'])->findOrFail($id);
        return $promotion;
    }

    /**
     * Tạo khuyến mại mới
     */
    public function createPromotion(array $data): Promotion
    {
        // 1. Validate dữ liệu
        $validator = $this->getValidator($data);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        try {
            DB::beginTransaction();
            
            // 2. Xử lý dữ liệu
            $data = $this->processData($data);
            
            // 3. Tạo promotion
            $promotion = Promotion::create($data);
            
            // 4. Xử lý relationships
            $this->syncPromotionScope($promotion, $data);
            
            // 5. Upload hình ảnh nếu có
            if (isset($data['image']) && $data['image']) {
                $this->handleImageUpload($promotion, $data['image']);
            }
            
            DB::commit();
            return $promotion;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating promotion: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cập nhật khuyến mại
     */
    public function updatePromotion(int $id, array $data): Promotion
    {
        // 1. Validate dữ liệu
        $validator = $this->getValidator($data, $id);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        try {
            DB::beginTransaction();
            
            // 2. Lấy promotion
            $promotion = Promotion::findOrFail($id);
            
            // 3. Xử lý dữ liệu
            $data = $this->processData($data);
            
            // 4. Cập nhật promotion
            $promotion->update($data);
            
            // 5. Xử lý relationships
            $this->syncPromotionScope($promotion, $data);
            
            // 6. Upload hình ảnh mới nếu có
            if (isset($data['image']) && $data['image']) {
                $this->handleImageUpload($promotion, $data['image']);
            }
            
            DB::commit();
            return $promotion;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating promotion: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Xóa khuyến mại
     */
    public function deletePromotion(int $id): array
    {
        try {
            $promotion = Promotion::findOrFail($id);
            
            // Xóa hình ảnh nếu có
            if ($promotion->image) {
                Storage::delete($promotion->image);
            }
            
            $promotion->delete();
            
            return [
                'success' => true,
                'message' => 'Xóa khuyến mại thành công!'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error deleting promotion: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa khuyến mại.'
            ];
        }
    }

    /**
     * Toggle trạng thái
     */
    public function toggleStatus(int $id, string $type): array
    {
        try {
            $promotion = Promotion::findOrFail($id);
            
            switch ($type) {
                case 'active':
                    $promotion->is_active = !$promotion->is_active;
                    $message = $promotion->is_active ? 'kích hoạt' : 'tạm dừng';
                    break;
                    
                case 'featured':
                    $promotion->is_featured = !$promotion->is_featured;
                    $message = $promotion->is_featured ? 'đặt nổi bật' : 'bỏ nổi bật';
                    break;
                    
                default:
                    throw new \InvalidArgumentException('Invalid status type.');
            }
            
            $promotion->save();
            
            return [
                'success' => true,
                'message' => "Đã $message khuyến mại thành công!",
                'data' => [
                    'is_active' => $promotion->is_active,
                    'is_featured' => $promotion->is_featured
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Error toggling promotion status: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thay đổi trạng thái.'
            ];
        }
    }

    /**
     * Validator cho promotion
     */
    protected function getValidator(array $data, ?int $id = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:50|regex:/^[A-Z0-9_-]+$/|unique:promotions,code' . ($id ? ",$id" : ''),
            'description' => 'required|string',
            'terms_conditions' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'expired_at' => 'required|date|after:valid_from',
            'apply_scope' => 'required|in:all,room_types',
            'image' => 'nullable|image|max:2048', // 2MB max
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'can_combine' => 'boolean'
        ];
        
        // Thêm validation cho room_type_ids
        if (isset($data['apply_scope']) && $data['apply_scope'] === 'room_types') {
            $rules['room_type_ids'] = 'required|array|min:1';
            $rules['room_type_ids.*'] = 'required|exists:room_types,id';
        }
        
        // Custom messages
        $messages = [
            'code.regex' => 'Mã khuyến mại chỉ được chứa chữ cái in hoa, số và dấu gạch ngang.',
            'expired_at.after' => 'Ngày hết hạn phải sau ngày bắt đầu.',
            'room_type_ids.required' => 'Vui lòng chọn ít nhất một loại phòng.',

            'image.max' => 'Hình ảnh không được vượt quá 2MB.'
        ];
        
        return Validator::make($data, $rules, $messages);
    }

    /**
     * Xử lý dữ liệu trước khi lưu
     */
    protected function processData(array $data): array
    {
        // 1. Xử lý các trường boolean
        $data['is_active'] = $data['is_active'] ?? false;
        $data['is_featured'] = $data['is_featured'] ?? false;
        $data['can_combine'] = $data['can_combine'] ?? false;
        
        // 2. Xử lý các trường số
        $data['minimum_amount'] = !empty($data['minimum_amount']) ? (float)$data['minimum_amount'] : 0;
        $data['usage_limit'] = !empty($data['usage_limit']) ? (int)$data['usage_limit'] : null;
        
        // 3. Xử lý ngày tháng
        $data['valid_from'] = !empty($data['valid_from']) ? $data['valid_from'] : null;
        
        // 4. Xử lý discount value
        if ($data['discount_type'] === 'percentage') {
            $data['discount_value'] = min((float)$data['discount_value'], 100);
        }
        
        // 5. Xử lý apply_scope dựa trên dữ liệu gửi lên
        if (!empty($data['room_type_ids'])) {
            $data['apply_scope'] = 'room_types';
        } else {
            $data['apply_scope'] = 'all';
        }
        
        // Log để debug
        Log::info('Processed promotion data', [
            'apply_scope' => $data['apply_scope'],
            'has_room_types' => !empty($data['room_type_ids'])
        ]);
        
        return $data;
    }

    /**
     * Xử lý upload hình ảnh
     */
    protected function handleImageUpload(Promotion $promotion, $image): void
    {
        // Xóa ảnh cũ nếu có
        if ($promotion->image) {
            Storage::delete($promotion->image);
        }
        
        // Upload ảnh mới
        $path = $image->store('promotions', 'public');
        $promotion->update(['image' => $path]);
    }

    /**
     * Xử lý relationships của promotion
     */
    protected function syncPromotionScope(Promotion $promotion, array $data): void
    {
        try {
            $applyScope = $data['apply_scope'] ?? 'all';
            
            Log::info('Syncing promotion scope', [
                'promotion_id' => $promotion->id,
                'apply_scope' => $applyScope,
                'room_type_ids' => $data['room_type_ids'] ?? []
            ]);
            
            switch ($applyScope) {
                case 'room_types':
                    // Validate room type IDs
                    $roomTypeIds = array_filter($data['room_type_ids'] ?? [], function($id) {
                        return is_numeric($id) && $id > 0;
                    });
                    
                    if (empty($roomTypeIds)) {
                        throw new \InvalidArgumentException('Vui lòng chọn ít nhất một loại phòng.');
                    }
                    
                    // Sync room types
                    $promotion->roomTypes()->sync($roomTypeIds);
                    Log::info('Synced room types', ['room_type_ids' => $roomTypeIds]);
                    break;
                    
                case 'all':
                default:
                    // Clear room types for "all" scope
                    $promotion->roomTypes()->sync([]);
                    Log::info('Applying to all rooms - no specific restrictions');
                    break;
            }
            
            // Clear cache
            $promotion->clearApplyCache();
            
        } catch (\Exception $e) {
            Log::error('Error in syncPromotionScope', [
                'error' => $e->getMessage(),
                'promotion_id' => $promotion->id ?? null,
                'apply_scope' => $applyScope ?? null,
                'data' => $data
            ]);
            throw $e;
        }
    }
} 