<?php

namespace App\Providers;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Interfaces\Services\PasswordResetServiceInterface;
use App\Services\PasswordResetService;
use App\Models\Booking;
use App\Observers\BookingObserver;


use App\Models\BookingNote;
use App\Observers\BookingNoteObserver;
use App\Models\RoomTypeReview;
use App\Observers\RoomTypeReviewObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Đăng ký Repository Bindings
        $this->app->bind(
            \App\Interfaces\Repositories\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );

        $this->app->bind(
            \App\Interfaces\Repositories\BookingRepositoryInterface::class,
            \App\Repositories\BookingRepository::class
        );

        $this->app->bind(
            \App\Interfaces\Repositories\RoomRepositoryInterface::class,
            \App\Repositories\RoomRepository::class
        );



        // Room Type Review Repository Binding
        $this->app->bind(
            \App\Interfaces\Repositories\RoomTypeReviewRepositoryInterface::class,
            \App\Repositories\RoomTypeReviewRepository::class
        );

        // Admin Repository Bindings
        $this->app->bind(
            \App\Interfaces\Repositories\Admin\AdminBookingRepositoryInterface::class,
            \App\Repositories\Admin\AdminBookingRepository::class
        );


        // Đăng ký Service Bindings
        $this->app->bind(
            \App\Interfaces\Services\AuthServiceInterface::class,
            \App\Services\AuthService::class
        );

        $this->app->bind(
            \App\Interfaces\Services\BookingServiceInterface::class,
            \App\Services\BookingService::class
        );



        // Room Type Review Service Binding
        $this->app->bind(
            \App\Interfaces\Services\RoomTypeReviewServiceInterface::class,
            \App\Services\RoomTypeReviewService::class
        );

        // Admin Service Bindings
        $this->app->bind(
            \App\Interfaces\Services\Admin\AdminBookingServiceInterface::class,
            \App\Services\Admin\AdminBookingService::class
        );

        $this->app->bind(
            \App\Interfaces\Services\Admin\AdminBookingServiceServiceInterface::class,
            \App\Services\Admin\AdminBookingServiceService::class
        );

        $this->app->bind(
            \App\Interfaces\Services\Admin\AdminDashboardServiceInterface::class,
            \App\Services\Admin\AdminDashboardService::class
        );

        // Password Reset Bindings

        // Profile Service Binding
        $this->app->bind(
            \App\Interfaces\Services\ProfileServiceInterface::class,
            function ($app) {
                return new \App\Services\ProfileService(
                    $app->make(\App\Interfaces\Repositories\UserRepositoryInterface::class)
                );
            }
        );

        $this->app->bind(PasswordResetServiceInterface::class, function ($app) {
            return new PasswordResetService(
                $app->make(UserRepositoryInterface::class)
            );
        });

        //Search Room Service Binding
        $this->app->bind(
            \App\Interfaces\Services\RoomServiceInterface::class,
            \App\Services\RoomService::class
        );

        $this->app->bind(
            \App\Interfaces\Services\UserServiceInterface::class,
            \App\Services\UserService::class
        );

        $this->app->bind(
            \App\Interfaces\Repositories\Admin\AdminRoomRepositoryInterface::class,
            \App\Repositories\Admin\AdminRoomRepository::class
        );

        $this->app->bind(
            \App\Interfaces\Services\Admin\AdminRoomServiceInterface::class,
            \App\Services\Admin\AdminRoomService::class
        );

        $this->app->bind(
            \App\Interfaces\Repositories\Admin\AdminRoomTypeRepositoryInterface::class,
            \App\Repositories\Admin\AdminRoomTypeRepository::class
        );

        $this->app->bind(
            \App\Interfaces\Services\Admin\AdminRoomTypeServiceInterface::class,
            \App\Services\Admin\AdminRoomTypeService::class
        );

        // Promotion Bindings
        $this->app->bind(
            \App\Interfaces\Repositories\PromotionRepositoryInterface::class,
            \App\Repositories\PromotionRepository::class
        );

        $this->app->bind(
            \App\Interfaces\Services\PromotionServiceInterface::class,
            \App\Services\PromotionService::class
        );

        // Admin Promotion Bindings
        $this->app->bind(
            \App\Interfaces\Repositories\Admin\AdminPromotionRepositoryInterface::class,
            \App\Repositories\Admin\AdminPromotionRepository::class
        );

        $this->app->bind(
            \App\Interfaces\Services\Admin\AdminPromotionServiceInterface::class,
            \App\Services\Admin\AdminPromotionService::class
        );

        $this->app->bind(
            \App\Interfaces\Repositories\RoomTypeRepositoryInterface::class,
            \App\Repositories\RoomTypeRepository::class
        );
        $this->app->bind(
            \App\Interfaces\Services\RoomTypeServiceInterface::class,
            \App\Services\RoomTypeService::class
        );

        $this->app->bind(
            \App\Interfaces\Services\SupportServiceInterface::class,
            \App\Services\SupportService::class
        );

        $this->app->bind(
            \App\Interfaces\Repositories\SupportTicketRepositoryInterface::class,
            \App\Repositories\SupportTicketRepository::class
        );

        // Service Category Repository Binding
        $this->app->bind(
            \App\Interfaces\Repositories\ServiceCategoryRepositoryInterface::class,
            \App\Repositories\ServiceCategoryRepository::class
        );
        // Service Category Service Binding
        $this->app->bind(
            \App\Interfaces\Services\ServiceCategoryServiceInterface::class,
            \App\Services\ServiceCategoryService::class
        );
        // Service Repository Binding
        $this->app->bind(
            \App\Interfaces\Repositories\ServiceRepositoryInterface::class,
            \App\Repositories\ServiceRepository::class
        );
        // Service Service Binding
        $this->app->bind(
            \App\Interfaces\Services\ServiceServiceInterface::class,
            \App\Services\ServiceService::class
        );
        // Room Type Service Repository Binding
        $this->app->bind(
            \App\Interfaces\Repositories\RoomTypeServiceRepositoryInterface::class,
            \App\Repositories\RoomTypeServiceRepository::class
        );
        // Room Type Service Service Binding
        $this->app->bind(
            \App\Interfaces\Services\RoomTypeServiceServiceInterface::class,
            \App\Services\RoomTypeServiceService::class
        );

        // View Composer cho dropdown notification
        \Illuminate\Support\Facades\View::composer('admin.layouts.admin-master', function ($view) {
            $unreadNotifications = \App\Models\AdminNotification::unread()->orderBy('created_at', 'desc')->limit(5)->get();
            $unreadCount = \App\Models\AdminNotification::unread()->count();
            $view->with(compact('unreadNotifications', 'unreadCount'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        
        // Đăng ký Observer
        Booking::observe(BookingObserver::class);
        BookingNote::observe(BookingNoteObserver::class);
        RoomTypeReview::observe(RoomTypeReviewObserver::class);
    }
}
