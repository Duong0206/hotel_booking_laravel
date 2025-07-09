<?php

namespace App\Interfaces\Services;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PromotionServiceInterface
{
    /**
     * Lấy tất cả promotion có phân trang
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPromotions(int $perPage = 12): LengthAwarePaginator;

    /**
     * Lấy promotion đang hoạt động
     *
     * @return Collection
     */
    public function getActivePromotions(): Collection;

    /**
     * Lấy promotion nổi bật cho trang chủ
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeaturedPromotions(int $limit = 3): Collection;

    /**
     * Lấy chi tiết promotion
     *
     * @param int $id
     * @return Promotion
     * @throws \Exception
     */
    public function getPromotionDetail(int $id): Promotion;

    /**
     * Kiểm tra và lấy promotion theo mã code
     *
     * @param string $code
     * @return Promotion
     * @throws \Exception
     */
    public function validatePromotionCode(string $code): Promotion;

    /**
     * Áp dụng promotion cho đơn hàng
     *
     * @param string $code
     * @param float $amount
     * @return array
     * @throws \Exception
     */
    public function applyPromotion(string $code, float $amount): array;

    /**
     * Lấy dữ liệu cho trang danh sách promotion
     *
     * @param array $filters
     * @return array
     */
    public function getPromotionPageData(array $filters = []): array;

    /**
     * Tính toán giảm giá
     *
     * @param Promotion $promotion
     * @param float $amount
     * @return float
     */
    public function calculateDiscount(Promotion $promotion, float $amount): float;

    /**
     * Đánh dấu promotion đã được sử dụng
     *
     * @param int $promotionId
     * @return bool
     */
    public function markAsUsed(int $promotionId): bool;
} 