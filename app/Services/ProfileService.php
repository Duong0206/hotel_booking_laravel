<?php

namespace App\Services;

use App\Interfaces\Services\ProfileServiceInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProfileService implements ProfileServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Lấy thông tin cá nhân của người dùng hiện tại
     */
    public function getProfileData(): array
    {
        $user = Auth::user();
        return [
            'user' => $user
        ];
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(array $data): bool
    {
        $user = Auth::user();
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $this->userRepository->update($user, $validator->validated());
        return true;
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(array $data): bool
    {
        $user = Auth::user();
        $validator = Validator::make($data, [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không đúng.'],
            ]);
        }
        $this->userRepository->update($user, [
            'password' => $data['password'],
        ]);
        return true;
    }

    /**
     * Lấy dữ liệu dashboard tổng quan cho user
     */
    public function getDashboardData(): array
    {
        try {
            $user = Auth::user();
            $userId = $user->id;
            
            // Tổng số booking
            $totalBookings = app(\App\Interfaces\Repositories\BookingRepositoryInterface::class)->getByUserId($userId)->count();
            // Số booking hoàn thành
            $completedBookings = app(\App\Interfaces\Repositories\BookingRepositoryInterface::class)->getCompletedBookings($userId)->count();
            // Tổng số review
            $totalReviews = app(\App\Interfaces\Repositories\RoomTypeReviewRepositoryInterface::class)->getReviewsByUser($userId, 1000)->total();
            // Tổng tiền đã chi tiêu
            $totalSpent = app(\App\Interfaces\Repositories\BookingRepositoryInterface::class)->getByUserId($userId)->sum('price');
            
            // Booking theo tháng (12 tháng gần nhất)
            $bookings = app(\App\Interfaces\Repositories\BookingRepositoryInterface::class)->getByUserId($userId);
            $monthly = array_fill(1, 12, 0);
            foreach ($bookings as $b) {
                $month = $b->created_at->format('n');
                $monthly[(int)$month]++;
            }
            
            // Thống kê đánh giá
            $reviews = app(\App\Interfaces\Repositories\RoomTypeReviewRepositoryInterface::class)->getReviewsByUser($userId, 1000);
            $approvedReviews = 0;
            $rejectedReviews = 0;
            $ratingCounts = array_fill(1, 5, 0);
            $totalRating = 0;
            $reviewCount = 0;
            
            foreach ($reviews as $review) {
                if ($review->status === 'approved') {
                    $approvedReviews++;
                } elseif ($review->status === 'rejected') {
                    $rejectedReviews++;
                }
                
                if ($review->rating >= 1 && $review->rating <= 5) {
                    $ratingCounts[$review->rating]++;
                    $totalRating += $review->rating;
                    $reviewCount++;
                }
            }
            
            $averageRating = $reviewCount > 0 ? $totalRating / $reviewCount : 0;
            
            // Số booking đang chờ
            $pendingBookings = app(\App\Interfaces\Repositories\BookingRepositoryInterface::class)->getByUserId($userId)->where('status', 'pending')->count();
            
            return [
                'totalBookings' => $totalBookings,
                'completedBookings' => $completedBookings,
                'pendingBookings' => $pendingBookings,
                'totalReviews' => $totalReviews,
                'approvedReviews' => $approvedReviews,
                'rejectedReviews' => $rejectedReviews,
                'totalSpent' => $totalSpent,
                'averageRating' => $averageRating,
                'monthlyBookings' => $monthly,
                'ratingCounts' => $ratingCounts,
            ];
        } catch (\Exception $e) {
            // Trả về dữ liệu mặc định nếu có lỗi
            return [
                'totalBookings' => 0,
                'completedBookings' => 0,
                'pendingBookings' => 0,
                'totalReviews' => 0,
                'approvedReviews' => 0,
                'rejectedReviews' => 0,
                'totalSpent' => 0,
                'averageRating' => 0,
                'monthlyBookings' => array_fill(1, 12, 0),
                'ratingCounts' => array_fill(1, 5, 0),
            ];
        }
    }
} 