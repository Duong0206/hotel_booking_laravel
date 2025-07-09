<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\AdminPromotionServiceInterface;
use App\Interfaces\Repositories\Admin\AdminPromotionRepositoryInterface;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminPromotionService implements AdminPromotionServiceInterface
{
    protected $promotionRepository;

    public function __construct(AdminPromotionRepositoryInterface $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    /**
     * Lấy danh sách promotion cho admin
     */
    public function getPromotions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->promotionRepository->getByFilters($filters, $perPage);
    }

    /**
     * Lấy chi tiết promotion
     */
    public function getPromotion(int $id): Promotion
    {
        $promotion = $this->promotionRepository->findById($id);
        
        if (!$promotion) {
            throw new \Exception('Không tìm thấy khuyến mại này.');
        }

        return $promotion;
    }

    /**
     * Tạo promotion mới
     */
    public function createPromotion(array $data): Promotion
    {
        Log::info('=== AdminPromotionService::createPromotion ===');
        Log::info('Input data', $data);
        
        $rules = $this->validatePromotionData($data);
        
        $validator = Validator::make($data, $rules['rules'], $rules['messages']);
        
        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Xử lý upload hình ảnh
        if (isset($data['image']) && $data['image']) {
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        // Xử lý các field số có thể null/empty
        $data['minimum_amount'] = $data['minimum_amount'] ?: 0;
        $data['usage_limit'] = $data['usage_limit'] ?: null;
        
        Log::info('Creating promotion with data', $data);
        
        // Tạo promotion
        $promotion = $this->promotionRepository->create($data);
        
        Log::info('Promotion created with ID', ['id' => $promotion->id]);

        // Sync room types hoặc rooms dựa vào apply_scope
        $this->syncPromotionScope($promotion, $data);

        return $promotion;
    }

    /**
     * Cập nhật promotion
     */
    public function updatePromotion(int $id, array $data): bool
    {
        $promotion = $this->getPromotion($id);
        
        $rules = $this->validatePromotionData($data, $id);
        
        $validator = Validator::make($data, $rules['rules'], $rules['messages']);
        
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Xử lý upload hình ảnh mới
        if (isset($data['image']) && $data['image']) {
            // Xóa ảnh cũ
            if ($promotion->image) {
                Storage::disk('public')->delete($promotion->image);
            }
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        // Xử lý các field số có thể null/empty
        $data['minimum_amount'] = $data['minimum_amount'] ?: 0;
        $data['usage_limit'] = $data['usage_limit'] ?: null;

        $result = $this->promotionRepository->update($id, $data);

        // Sync room types hoặc rooms dựa vào apply_scope
        $this->syncPromotionScope($promotion, $data);

        return $result;
    }

    /**
     * Xóa promotion
     */
    public function deletePromotion(int $id): array
    {
        try {
            $promotion = $this->getPromotion($id);
            
            // Kiểm tra logic xóa theo trạng thái
            $isExpired = $promotion->expired_at < now();
            $isActive = $promotion->is_active;
            
            // Logic xóa mới:
            // - CHỈ được xóa khi "Sắp diễn ra" (chưa active và chưa hết hạn)
            // - KHÔNG được xóa khi "Đang hoạt động" 
            // - KHÔNG được xóa khi "Đã kết thúc"
            
            if ($isExpired) {
                return [
                    'success' => false,
                    'message' => 'Không thể xóa khuyến mại đã kết thúc.'
                ];
            }
            
            if ($isActive) {
                return [
                    'success' => false,
                    'message' => 'Không thể xóa khuyến mại đang hoạt động. Vui lòng tắt kích hoạt trước.'
                ];
            }
            
            // Chỉ cho phép xóa khi: !$isActive && !$isExpired (tức là "Sắp diễn ra")

            // Xóa hình ảnh nếu có
            if ($promotion->image) {
                Storage::disk('public')->delete($promotion->image);
            }

            $this->promotionRepository->delete($id);

            return [
                'success' => true,
                'message' => 'Xóa khuyến mại thành công.'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate dữ liệu promotion
     */
    public function validatePromotionData(array $data, ?int $id = null): array
    {
        // Tạo unique rule cho code sử dụng Rule class
        $codeRules = ['required', 'string', 'max:50', 'alpha_dash'];
        if ($id) {
            $codeRules[] = Rule::unique('promotions', 'code')->ignore($id);
        } else {
            $codeRules[] = 'unique:promotions,code';
        }

        // Tạo date rule cho expired_at
        $dateRule = $id ? 'required|date' : 'required|date|after:today';

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => $codeRules,
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date|before_or_equal:expired_at',
            'expired_at' => $dateRule,
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'can_combine' => 'boolean',
            'apply_scope' => 'nullable|in:all,room_types,specific_rooms',
            'room_type_ids' => 'nullable|array',
            'room_type_ids.*' => 'exists:room_types,id',
            'room_ids' => 'nullable|array', 
            'room_ids.*' => 'exists:rooms,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'terms_conditions' => 'nullable|string'
        ];

        // Validation riêng cho discount_value theo discount_type
        if (isset($data['discount_type'])) {
            if ($data['discount_type'] === 'percentage') {
                $rules['discount_value'] = 'required|numeric|min:0|max:100';
            } else {
                $rules['discount_value'] = 'required|numeric|min:0';
            }
        }

        $messages = [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.required' => 'Mô tả là bắt buộc.',
            'code.required' => 'Mã khuyến mại là bắt buộc.',
            'code.unique' => 'Mã khuyến mại đã tồn tại.',
            'code.alpha_dash' => 'Mã chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới.',
            'discount_type.required' => 'Loại giảm giá là bắt buộc.',
            'discount_type.in' => 'Loại giảm giá không hợp lệ.',
            'discount_value.required' => 'Giá trị giảm giá là bắt buộc.',
            'discount_value.min' => 'Giá trị giảm giá phải lớn hơn 0.',
            'discount_value.max' => 'Giá trị giảm giá phần trăm không được vượt quá 100.',
            'valid_from.date' => 'Ngày bắt đầu phải là ngày hợp lệ.',
            'valid_from.before_or_equal' => 'Ngày bắt đầu phải trước hoặc bằng ngày hết hạn.',
            'expired_at.required' => 'Ngày hết hạn là bắt buộc.',
            'expired_at.after' => 'Ngày hết hạn phải sau ngày hôm nay.',
            'image.image' => 'File phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2MB.'
        ];

        return [
            'rules' => $rules,
            'messages' => $messages
        ];
    }

    /**
     * Lấy thống kê promotion
     */
    public function getStats(): array
    {
        return $this->promotionRepository->getStats();
    }

    /**
     * Toggle trạng thái promotion
     */
    public function toggleStatus(int $id, string $type): array
    {
        try {
            $success = false;
            
            switch ($type) {
                case 'active':
                    $success = $this->promotionRepository->toggleActive($id);
                    $message = 'Cập nhật trạng thái thành công.';
                    break;
                    
                case 'featured':
                    $success = $this->promotionRepository->toggleFeatured($id);
                    $message = 'Cập nhật trạng thái nổi bật thành công.';
                    break;
                    
                default:
                    throw new \Exception('Loại trạng thái không hợp lệ.');
            }

            return [
                'success' => $success,
                'message' => $success ? $message : 'Có lỗi xảy ra khi cập nhật.'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Sync promotion scope (room types hoặc specific rooms)
     */
    protected function syncPromotionScope($promotion, array $data): void
    {
        $applyScope = $data['apply_scope'] ?? 'all';
        
        Log::info('=== syncPromotionScope ===');
        Log::info('Apply scope', ['scope' => $applyScope]);
        Log::info('Data', $data);
        
        switch ($applyScope) {
            case 'room_types':
                // Sync room types, clear rooms
                $roomTypeIds = $data['room_type_ids'] ?? [];
                Log::info('Syncing room types', ['room_type_ids' => $roomTypeIds]);
                $promotion->roomTypes()->sync($roomTypeIds);
                $promotion->rooms()->sync([]);
                break;
                
            case 'specific_rooms':
                // Sync specific rooms, clear room types  
                $roomIds = $data['room_ids'] ?? [];
                Log::info('Syncing specific rooms', ['room_ids' => $roomIds]);
                $promotion->rooms()->sync($roomIds);
                $promotion->roomTypes()->sync([]);
                break;
                
            case 'all':
            default:
                // Clear both - apply to all rooms
                Log::info('Clearing all room relationships - apply to all');
                $promotion->roomTypes()->sync([]);
                $promotion->rooms()->sync([]);
                break;
        }
        
        Log::info('Sync completed');
    }

    /**
     * Xử lý upload hình ảnh
     */
    public function handleImageUpload($image): ?string
    {
        if (!$image) {
            return null;
        }

        $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('promotions', $fileName, 'public');
        
        return $path;
    }
} 