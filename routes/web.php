<?php

use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminRoomController;
use App\Http\Controllers\Admin\AdminRoomTypeReviewController;
use App\Http\Controllers\Admin\AdminRoomTypeServiceController;
use App\Http\Controllers\Admin\AdminServiceCategoryController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminSupportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeReviewController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('client.index');
// });

Route::get('/', [HotelController::class, 'index'])->name('index');

Route::get('/rooms', [HotelController::class, 'rooms'])->name('rooms');

Route::get('/restaurant', [HotelController::class, 'restaurant'])->name('restaurant');

Route::get('/blog', [HotelController::class, 'blog'])->name('blog');

Route::get('/about', [HotelController::class, 'about'])->name('about');

Route::get('/contact', [HotelController::class, 'contact'])->name('contact');
Route::post('/contact', [HotelController::class, 'sendContact'])->name('contact.send');

Route::get('/rooms-single/{id}', [HotelController::class, 'roomsSingle'])->name('rooms-single');

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
Route::middleware(['auth'])->group(function () {
    // Booking - Đặt phòng
    Route::get('/booking', [BookingController::class, 'booking'])->name('booking');
    Route::get('/booking/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
    Route::post('/booking', [BookingController::class, 'storeBooking']);
    Route::post('/booking/cancel/{id}', [BookingController::class, 'cancelBooking'])->name('booking.cancel');

    // Support
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/support/conversation/{conversationId}', [SupportController::class, 'showConversation'])->name('support.showConversation');
    Route::post('/support/conversation/{conversationId}/message', [SupportController::class, 'sendMessage'])->name('support.sendMessage');
    Route::post('/support/message', [SupportController::class, 'sendMessage'])->name('support.createMessage');
    Route::get('/support/conversation/{conversationId}/messages', [SupportController::class, 'getNewMessages'])->name('support.getNewMessages');
    Route::get('/booking/{id}/detail', [BookingController::class, 'showDetail'])->name('booking.detail');

    // AJAX Room Type Reviews - Đặt trước để tránh xung đột
    Route::post('/room-type-reviews/store-ajax', [RoomTypeReviewController::class, 'storeAjax'])->name('room-type-reviews.store-ajax');

    // Room Type Reviews - Chỉ trong profile
    Route::post('/room-type-reviews/{roomTypeId}', [UserProfileController::class, 'createReview'])->name('room-type-reviews.store');
    Route::put('/room-type-reviews/{id}', [UserProfileController::class, 'updateReview'])->name('room-type-reviews.update');
    Route::delete('/room-type-reviews/{id}', [UserProfileController::class, 'deleteReview'])->name('room-type-reviews.destroy');

    // Form đánh giá
    Route::get('/room-type-reviews/{roomTypeId}/form', [RoomTypeReviewController::class, 'reviewForm'])->name('room-type-reviews.form');
});

// Booking notes: KHÔNG lồng group auth bên ngoài nữa
Route::middleware(['auth', 'check.booking.access'])->group(function () {
    Route::get('/booking-notes/{bookingId}', [BookingController::class, 'notesIndex'])->name('booking-notes.index');
    Route::get('/booking-notes/{bookingId}/search', [BookingController::class, 'notesSearch'])->name('booking-notes.search');
    Route::get('/booking-notes/{bookingId}/create', [BookingController::class, 'notesCreate'])->name('booking-notes.create');
    Route::post('/booking-notes/{bookingId}', [BookingController::class, 'notesStore'])->name('booking-notes.store');
    Route::get('/booking-notes/{bookingId}/{id}/edit', [BookingController::class, 'notesEdit'])->name('booking-notes.edit');
    Route::put('/booking-notes/{bookingId}/{id}', [BookingController::class, 'notesUpdate'])->name('booking-notes.update');
    Route::delete('/booking-notes/{bookingId}/{id}', [BookingController::class, 'notesDestroy'])->name('booking-notes.destroy');
    Route::get('/booking-notes/{bookingId}/{id}', [BookingController::class, 'notesShow'])->name('booking-notes.show');
    Route::post('/booking-notes/{bookingId}/request', [BookingController::class, 'notesStoreRequest'])->name('booking-notes.store-request');
    Route::post('/booking-notes/{bookingId}/response', [BookingController::class, 'notesStoreResponse'])->name('booking-notes.store-response');
});

// Routes công khai cho room type reviews (chỉ hiển thị)
Route::get('/rooms/{id}/reviews-ajax', [HotelController::class, 'getRoomReviewsAjax'])->name('rooms.reviews-ajax');

// Admin Routes
Route::prefix('/admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    // Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Quản lý đặt phòng
    Route::get('bookings/report', [AdminBookingController::class, 'report'])->name('bookings.report');
    Route::patch('bookings/{id}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::resource('bookings', AdminBookingController::class);

    // Quản lý thông báo
    Route::get('notifications', [AdminBookingController::class, 'notificationsIndex'])->name('notifications.index');
    Route::get('notifications/{id}', [AdminBookingController::class, 'notificationShow'])->name('notifications.show');
    Route::delete('notifications/{id}', [AdminBookingController::class, 'destroy'])->name('notifications.destroy');

    // API thông báo
    Route::prefix('api/notifications')->name('notifications.')->group(function () {
        Route::get('unread', [AdminBookingController::class, 'getUnreadNotifications'])->name('unread');
        Route::get('count', [AdminBookingController::class, 'getUnreadCount'])->name('count');
        Route::get('list', [AdminBookingController::class, 'getNotifications'])->name('list');
        Route::post('mark-read', [AdminBookingController::class, 'markAsRead'])->name('mark-read');
        Route::post('mark-all-read', [AdminBookingController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('delete', [AdminBookingController::class, 'deleteNotification'])->name('delete');
        Route::delete('delete-read', [AdminBookingController::class, 'deleteReadNotifications'])->name('delete-read');
        Route::delete('delete-old', [AdminBookingController::class, 'deleteOldNotifications'])->name('delete-old');
        Route::post('test', [AdminBookingController::class, 'createTestNotification'])->name('test');
        Route::post('test-note', [AdminBookingController::class, 'createTestNoteNotification'])->name('test-note');
        Route::post('test-review', [AdminBookingController::class, 'createTestReviewNotification'])->name('test-review');
        Route::post('delete-multi', [AdminBookingController::class, 'deleteNotifications'])->name('delete-multi');
        Route::post('mark-read-multi', [AdminBookingController::class, 'markNotificationsAsRead'])->name('mark-read-multi');
    });

    // Quản lý dịch vụ booking
    Route::post('bookings/{id}/services/add', [AdminBookingController::class, 'addServiceToBooking'])->name('bookings.services.add');
    Route::delete('bookings/{id}/services/{bookingServiceId}', [AdminBookingController::class, 'destroyServiceFromBooking'])->name('bookings.services.destroy');
    Route::post('bookings/{id}/confirm-payment', [AdminBookingController::class, 'confirmPayment'])->name('bookings.confirm-payment');

    // Quản lý phòng
    Route::resource('rooms', AdminRoomController::class);

    Route::get('/support', [AdminSupportController::class, 'index'])->name('support.index');
    Route::get('/support/conversation/{conversationId}', [AdminSupportController::class, 'showConversation'])->name('support.showConversation');
    Route::post('/support/conversation/{conversationId}/message', [AdminSupportController::class, 'sendMessage'])->name('support.sendMessage');
    Route::get('/support/conversation/{conversationId}/messages', [AdminSupportController::class, 'getNewMessages'])->name('support.getNewMessages');
    Route::post('/support/conversations/updates', [AdminSupportController::class, 'getUpdates'])->name('support.getUpdates');

    // User status routes
    Route::get('/support/conversation/{conversationId}/user-status', [AdminSupportController::class, 'getUserStatus'])->name('support.getUserStatus');
    Route::post('/support/conversation/{conversationId}/status', [AdminSupportController::class, 'updateUserStatus'])->name('support.updateUserStatus');

    // Routes cho quản lý ảnh phòng
    Route::delete('rooms/{room}/images/{image}', [AdminRoomController::class, 'deleteImage'])->name('rooms.images.delete');
    Route::post('rooms/{room}/images/{image}/primary', [AdminRoomController::class, 'setPrimaryImage'])->name('rooms.images.primary');

    // Quản lý đánh giá loại phòng
    Route::get('room-type-reviews/statistics', [AdminRoomTypeReviewController::class, 'statistics'])->name('room-type-reviews.statistics');
    Route::patch('room-type-reviews/{id}/approve', [AdminRoomTypeReviewController::class, 'approve'])->name('room-type-reviews.approve');
    Route::patch('room-type-reviews/{id}/reject', [AdminRoomTypeReviewController::class, 'reject'])->name('room-type-reviews.reject');
    Route::get('room-type-reviews', [AdminRoomTypeReviewController::class, 'index'])->name('room-type-reviews.index');
    Route::get('room-type-reviews/create', [AdminRoomTypeReviewController::class, 'create'])->name('room-type-reviews.create');
    Route::post('room-type-reviews', [AdminRoomTypeReviewController::class, 'store'])->name('room-type-reviews.store');
    Route::get('room-type-reviews/{review}', [AdminRoomTypeReviewController::class, 'show'])->name('room-type-reviews.show');
    Route::delete('room-type-reviews/{review}', [AdminRoomTypeReviewController::class, 'destroy'])->name('room-type-reviews.destroy');

    // Quản lý người dùng
    Route::resource('users', AdminUserController::class)->except(['create', 'store']);

    // Quản lý danh mục dịch vụ (Service Categories)
    Route::get('service-categories', [AdminServiceCategoryController::class, 'index'])->name('service-categories.index');
    Route::get('service-categories/create', [AdminServiceCategoryController::class, 'create'])->name('service-categories.create');
    Route::post('service-categories', [AdminServiceCategoryController::class, 'store'])->name('service-categories.store');
    Route::get('service-categories/{service_category}/edit', [AdminServiceCategoryController::class, 'edit'])->name('service-categories.edit');
    Route::put('service-categories/{service_category}', [AdminServiceCategoryController::class, 'update'])->name('service-categories.update');
    Route::delete('service-categories/{service_category}', [AdminServiceCategoryController::class, 'destroy'])->name('service-categories.destroy');

    // Quản lý dịch vụ (Services)
    Route::get('services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::get('services/create', [AdminServiceController::class, 'create'])->name('services.create');
    Route::post('services', [AdminServiceController::class, 'store'])->name('services.store');
    Route::get('services/{service}/edit', [AdminServiceController::class, 'edit'])->name('services.edit');
    Route::put('services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');

    // Gán dịch vụ cho loại phòng (Room Type Services)
    Route::get('room-type-services', [AdminRoomTypeServiceController::class, 'index'])->name('room-type-services.index');
    Route::get('room-type-services/{room_type}/edit', [AdminRoomTypeServiceController::class, 'edit'])->name('room-type-services.edit');
    Route::put('room-type-services/{room_type}', [AdminRoomTypeServiceController::class, 'update'])->name('room-type-services.update');
});

// Route công khai cho room type reviews (chỉ hiển thị) - đặt sau admin routes để tránh xung đột
Route::get('/room-type-reviews/{roomTypeId}/ajax', [RoomTypeReviewController::class, 'getReviewsAjax'])->name('room-type-reviews.ajax');

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
Route::get('/search-rooms', [RoomController::class, 'search'])->name('rooms.search');

// Payment
Route::get('/confirm-info-payment/{booking}', [PaymentController::class, 'confirmInfo'])->name('confirm-info-payment');
Route::get('/payment-method/{booking?}', [PaymentController::class, 'paymentMethod'])->name('payment-method');
Route::post('/ajax-booking', [BookingController::class, 'ajaxStoreBooking'])->name('ajax-booking');
Route::get('/api/room-type/{id}/image', [BookingController::class, 'getRoomTypeImage'])->name('api.room-type.image');


Route::get('/payment/bank-transfer/{booking}', [PaymentController::class, 'processBankTransfer'])->name('payment.bank-transfer');
Route::post('/payment/bank-transfer/{booking}/confirm', [PaymentController::class, 'confirmBankTransfer'])->name('payment.bank-transfer.confirm');
Route::get('/payment/credit-card/{booking}', [PaymentController::class, 'processCreditCard'])->name('payment.credit-card');
Route::post('/payment/credit-card/{booking}/confirm', [PaymentController::class, 'confirmCreditCard'])->name('payment.credit-card.confirm');
Route::get('/payment/success/{booking}', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');
Route::get('/payment/history/{booking}', [PaymentController::class, 'history'])->name('payment.history');

// User Profile Routes (chỉ cần đăng nhập, không cần xác minh email)
Route::middleware('auth')->group(function () {
    Route::get('/user/profile', [UserProfileController::class, 'showUserProfile'])->name('user.profile');
    Route::post('/user/profile/update', [UserProfileController::class, 'updateUserProfile'])->name('user.profile.update');
    Route::post('/user/profile/change-password', [UserProfileController::class, 'changeUserPassword'])->name('user.profile.change_password');
    Route::get('/user/bookings', [UserProfileController::class, 'showUserBookings'])->name('user.bookings');
    Route::get('/user/bookings/partial', [BookingController::class, 'partial'])->name('user.bookings.partial');
    Route::get('/user/bookings/{id}/detail', [BookingController::class, 'detailPartial'])->name('user.bookings.detail.partial');
    Route::get('/user/notes/partial', [BookingController::class, 'notesPartial'])->name('user.notes.partial');
    Route::get('/user/reviews', [UserProfileController::class, 'showUserReviews'])->name('user.reviews');
    Route::get('/user/reviews/partial', [UserProfileController::class, 'partialReviews'])->name('user.reviews.partial');
    Route::get('/user/reviews/{id}/detail', [UserProfileController::class, 'reviewDetail'])->name('user.reviews.detail');
});
