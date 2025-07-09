<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\AdminRoomController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Route::get('/', function () {
//     return view('client.index');
// });

Route::get('/', [HotelController::class, 'index'])->name('index');

Route::get('/rooms', [HotelController::class, 'rooms'])->name('rooms');

Route::get('/restaurant', [HotelController::class, 'restaurant'])->name('restaurant');

Route::get('/blog', [HotelController::class, 'blog'])->name('blog');

Route::get('/about', [HotelController::class, 'about'])->name('about');

Route::get('/contact', [HotelController::class, 'contact'])->name('contact');

Route::get('/rooms-single/{id?}', [HotelController::class, 'roomsSingle'])->name('rooms-single');

Route::get('/blog-single', [HotelController::class, 'blogSingle'])->name('blog-single');

// Đăng nhập & Đăng ký
Route::middleware('guest')->group(function () {
    // Đăng nhập
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Đăng ký
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Đăng xuất
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Xác minh email
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');
        
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
        
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Routes yêu cầu xác thực email
Route::middleware(['auth', 'verified'])->group(function () {
    // Booking - Đặt phòng
    Route::get('/booking', [BookingController::class, 'booking'])->name('booking');
    Route::post('/booking', [BookingController::class, 'storeBooking']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings');
    Route::post('/booking/cancel/{id}', [BookingController::class, 'cancelBooking'])->name('booking.cancel');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Quản lý đặt phòng
    Route::get('bookings/report', [AdminBookingController::class, 'report'])->name('bookings.report');
    Route::patch('bookings/{id}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::resource('bookings', AdminBookingController::class);

    // Quản lý phòng
    Route::resource('rooms', AdminRoomController::class);

    // Routes cho quản lý ảnh phòng
    Route::delete('rooms/{room}/images/{image}', [AdminRoomController::class, 'deleteImage'])->name('rooms.images.delete');
    Route::post('rooms/{room}/images/{image}/primary', [AdminRoomController::class, 'setPrimaryImage'])->name('rooms.images.primary');

    // Quản lý khuyến mại
    Route::resource('promotions', App\Http\Controllers\Admin\AdminPromotionController::class);
    Route::post('promotions/{promotion}/toggle-status', [App\Http\Controllers\Admin\AdminPromotionController::class, 'toggleStatus'])->name('promotions.toggle-status');
    Route::post('promotions/bulk-action', [App\Http\Controllers\Admin\AdminPromotionController::class, 'bulkAction'])->name('promotions.bulk-action');
});

// Password reset routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])
    ->middleware('guest')
    ->name('password.request');

// Gửi link đặt lại mật khẩu
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
    ->middleware('guest')
    ->name('password.email');

// Hiển thị form đặt lại mật khẩu
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

// Đặt lại mật khẩu
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])
    ->middleware('guest')
    ->name('password.update');

// Search Rooms
Route::get('/rooms', [HotelController::class, 'rooms'])->name('rooms');
Route::get('/search-rooms', [RoomController::class, 'search'])->name('rooms.search');

// Payment
Route::get('/confirm-info-payment/{booking}', [PaymentController::class, 'confirmInfo'])->name('confirm-info-payment');
Route::get('/payment-method/{booking}', [PaymentController::class, 'paymentMethod'])->name('payment-method');

// Promotions
Route::get('/promotions', [App\Http\Controllers\PromotionController::class, 'index'])->name('promotions.index');
Route::get('/promotions/{id}', [App\Http\Controllers\PromotionController::class, 'show'])->name('promotions.show');
Route::post('/promotions/validate-code', [App\Http\Controllers\PromotionController::class, 'validateCode'])->name('promotions.validate-code');
Route::get('/api/promotions/featured', [App\Http\Controllers\PromotionController::class, 'getFeatured'])->name('promotions.featured');

