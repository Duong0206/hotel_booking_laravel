<?php

namespace App\Interfaces\Services\Admin;

use App\Models\Promotion;

interface AdminPromotionServiceInterface
{
    /**
     * Lấy danh sách khuyến mại có phân trang và lọc
     */
    public function getPromotions(array $filters = [], int $perPage = 15);

    /**
     * Lấy thống kê tổng quan
     */
    public function getStats(): array;

    /**
     * Lấy chi tiết khuyến mại
     */
    public function getPromotion(int $id): Promotion;

    /**
     * Tạo khuyến mại mới
     */
    public function createPromotion(array $data): Promotion;

    /**
     * Cập nhật khuyến mại
     */
    public function updatePromotion(int $id, array $data): Promotion;

    /**
     * Xóa khuyến mại
     */
    public function deletePromotion(int $id): array;

    /**
     * Toggle trạng thái
     */
    public function toggleStatus(int $id, string $type): array;
} 