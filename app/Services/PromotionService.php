<?php

namespace App\Services;

use App\Interfaces\Services\PromotionServiceInterface;
use App\Interfaces\Repositories\PromotionRepositoryInterface;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PromotionService implements PromotionServiceInterface
{
    protected $promotionRepository;

    public function __construct(PromotionRepositoryInterface $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    /**
     * Lấy tất cả promotion có phân trang
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPromotions(int $perPage = 12): LengthAwarePaginator
    {
        return $this->promotionRepository->getAllWithPagination($perPage);
    }

    /**
     * Lấy promotion đang hoạt động
     *
     * @return Collection
     */
    public function getActivePromotions(): Collection
    {
        return $this->promotionRepository->getActive();
    }

    /**
     * Lấy promotion nổi bật cho trang chủ
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeaturedPromotions(int $limit = 3): Collection
    {
        return $this->promotionRepository->getFeatured($limit);
    }

    /**
     * Lấy chi tiết promotion
     *
     * @param int $id
     * @return Promotion
     * @throws \Exception
     */
    public function getPromotionDetail(int $id): Promotion
    {
        $promotion = $this->promotionRepository->findById($id);
        
        if (!$promotion) {
            throw new \Exception('Không tìm thấy khuyến mại này.');
        }

        return $promotion;
    }

    /**
     * Kiểm tra và lấy promotion theo mã code
     *
     * @param string $code
     * @return Promotion
     * @throws \Exception
     */
    public function validatePromotionCode(string $code): Promotion
    {
        $promotion = $this->promotionRepository->findByCode($code);
        
        if (!$promotion) {
            throw new \Exception('Mã khuyến mại không tồn tại.');
        }

        if (!$promotion->isValid()) {
            throw new \Exception('Mã khuyến mại đã hết hạn hoặc không còn hiệu lực.');
        }

        return $promotion;
    }

    /**
     * Áp dụng promotion cho đơn hàng
     *
     * @param string $code
     * @param float $amount
     * @return array
     * @throws \Exception
     */
    public function applyPromotion(string $code, float $amount): array
    {
        $promotion = $this->validatePromotionCode($code);
        
        if ($amount < $promotion->minimum_amount) {
            throw new \Exception('Đơn hàng phải có giá trị tối thiểu ' . number_format((float)$promotion->minimum_amount, 0, ',', '.') . 'đ để áp dụng khuyến mại này.');
        }

        $discountAmount = $this->calculateDiscount($promotion, $amount);
        $finalAmount = $amount - $discountAmount;

        return [
            'promotion' => $promotion,
            'original_amount' => $amount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'discount_text' => $promotion->discount_text
        ];
    }

    /**
     * Lấy dữ liệu cho trang danh sách promotion
     *
     * @param array $filters
     * @return array
     */
    public function getPromotionPageData(array $filters = []): array
    {
        // Thêm filters mặc định cho active và available
        $defaultFilters = array_merge($filters, [
            'active' => true, 
            'available' => true
        ]);

        // Sử dụng phân trang tự động từ repository
        $perPage = 12;
        $promotions = $this->promotionRepository->getAllWithFilters($defaultFilters, $perPage);

        return [
            'promotions' => $promotions,
            'total_count' => $promotions->total(),
            'active_filters' => $filters
        ];
    }

    /**
     * Tính toán giảm giá
     *
     * @param Promotion $promotion
     * @param float $amount
     * @return float
     */
    public function calculateDiscount(Promotion $promotion, float $amount): float
    {
        return $promotion->calculateDiscount($amount);
    }

    /**
     * Đánh dấu promotion đã được sử dụng
     *
     * @param int $promotionId
     * @return bool
     */
    public function markAsUsed(int $promotionId): bool
    {
        return $this->promotionRepository->incrementUsedCount($promotionId);
    }

    /**
     * Kiểm tra và áp dụng mã giảm giá cho booking
     *
     * @param string $code
     * @param int $bookingId
     * @return array
     * @throws \Exception
     */
    public function checkAndApplyPromotion(string $code, int $bookingId): array
    {
        // Lấy thông tin booking
        $booking = \App\Models\Booking::findOrFail($bookingId);

        // Kiểm tra và lấy thông tin promotion
        $promotion = $this->validatePromotionCode($code);

        // Kiểm tra điều kiện áp dụng
        if ($booking->price < $promotion->minimum_amount) {
            throw new \Exception('Đơn đặt phòng phải có giá trị tối thiểu ' . number_format((float)$promotion->minimum_amount, 0, ',', '.') . 'đ để áp dụng khuyến mại này.');
        }

        // Tính toán giảm giá
        $discountAmount = $this->calculateDiscount($promotion, $booking->price);
        $finalPrice = $booking->price - $discountAmount;

        return [
            'discount_amount' => $discountAmount,
            'total_price' => $finalPrice
        ];
    }

    /**
     * Lấy danh sách khuyến mãi có thể áp dụng cho loại phòng
     *
     * @param int $roomTypeId
     * @param float $price
     * @return Collection
     */
    public function getAvailablePromotionsForRoomType(int $roomTypeId, float $price): Collection
    {
        return $this->promotionRepository->getAvailablePromotionsForRoomType($roomTypeId, $price);
    }
} 