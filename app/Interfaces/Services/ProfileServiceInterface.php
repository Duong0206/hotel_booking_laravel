<?php

namespace App\Interfaces\Services;

use App\Models\User;
use Illuminate\Http\Request;

interface ProfileServiceInterface
{
    /**
     * Lấy thông tin cá nhân của người dùng hiện tại
     *
     * @return array
     */
    public function getProfileData(): array;
    
    /**
     * Cập nhật thông tin cá nhân
     *
     * @param array $data
     * @return bool
     */
    public function updateProfile(array $data): bool;
    
    /**
     * Đổi mật khẩu
     *
     * @param array $data
     * @return bool
     */
    public function changePassword(array $data): bool;

    /**
     * Lấy dữ liệu dashboard tổng quan cho user
     * @return array
     */
    public function getDashboardData(): array;
} 