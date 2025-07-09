<?php

namespace App\Interfaces\Services\Admin;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdminPromotionServiceInterface
{
    /**
     * Lấy danh sách promotion cho admin
     */
    public function getPromotions(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Lấy chi tiết promotion
     */
    public function getPromotion(int $id): Promotion;

    /**
     * Tạo promotion mới
     */
    public function createPromotion(array $data): Promotion;

    /**
     * Cập nhật promotion
     */
    public function updatePromotion(int $id, array $data): bool;

    /**
     * Xóa promotion
     */
    public function deletePromotion(int $id): array;

    /**
     * Validate dữ liệu promotion
     */
    public function validatePromotionData(array $data, ?int $id = null): array;

    /**
     * Lấy thống kê promotion
     */
    public function getStats(): array;

    /**
     * Toggle trạng thái promotion
     */
    public function toggleStatus(int $id, string $type): array;

    /**
     * Xử lý upload hình ảnh
     */
    public function handleImageUpload($image): ?string;
} 