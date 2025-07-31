<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\PromotionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromotionController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionServiceInterface $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    /**
     * Hiển thị danh sách promotion
     */
    public function index(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'discount_type' => 'nullable|in:percentage,fixed',
                'search' => 'nullable|string|max:255'
            ]);

            $filters = $request->only(['discount_type', 'search']);
            $data = $this->promotionService->getPromotionPageData($filters);
            
            return view('client.promotions.index', $data);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('promotions.index')
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error in PromotionController@index: ' . $e->getMessage());
            return redirect()->route('index')
                ->with('error', 'Có lỗi xảy ra khi tải danh sách khuyến mại.');
        }
    }

    /**
     * Hiển thị chi tiết promotion
     */
    public function show($id)
    {
        try {
            // Validate ID
            if (!is_numeric($id) || $id <= 0) {
                throw new \Exception('ID khuyến mại không hợp lệ.');
            }

            $promotion = $this->promotionService->getPromotionDetail($id);
            
            // Lấy promotion liên quan (nổi bật khác)
            $relatedPromotions = $this->promotionService->getFeaturedPromotions(3)
                ->where('id', '!=', $id)
                ->take(2);
            return view('client.promotions.show', compact('promotion', 'relatedPromotions'));
        } catch (\Exception $e) {
            Log::error('Error in PromotionController@show: ' . $e->getMessage());
            return redirect()->route('promotions.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Kiểm tra mã promotion (Ajax)
     */
    public function validateCode(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:50',
                'amount' => 'required|numeric|min:0'
            ]);

            $result = $this->promotionService->applyPromotion(
                $request->code, 
                $request->amount
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Áp dụng mã khuyến mại thành công!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in PromotionController@validateCode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Lấy promotion nổi bật cho trang chủ
     */
    public function getFeatured()
    {
        try {
            $promotions = $this->promotionService->getFeaturedPromotions(3);
            
            return response()->json([
                'success' => true,
                'data' => $promotions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải khuyến mại nổi bật'
            ], 500);
        }
    }

    /**
     * Kiểm tra và áp dụng mã giảm giá cho booking
     */
    public function checkPromotion(Request $request)
    {
        try {
            $request->validate([
                'promotion_code' => 'required|string|max:50',
                'booking_id' => 'required|exists:bookings,id'
            ]);

            $result = $this->promotionService->checkAndApplyPromotion(
                $request->promotion_code,
                $request->booking_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công!',
                'discount_amount' => $result['discount_amount'],
                'total_price' => $result['total_price']
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in PromotionController@checkPromotion: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
} 