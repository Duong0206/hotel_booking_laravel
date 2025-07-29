@extends('client.layouts.master')

@section('title', 'Đặt Phòng Của Tôi')

@section('content')
<div class="hero-wrap" style="background-image: url('client/images/bg_1.jpg');">
    <div class="overlay"></div>
    <div class="container">
        <div class="row no-gutters slider-text d-flex align-itemd-end justify-content-center">
            <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
                <div class="text">
                    <p class="breadcrumbs mb-2"><span class="mr-2"><a href="{{ route('index') }}">Trang chủ</a></span> <span>Đặt Phòng Của Tôi</span></p>
                    <h1 class="mb-4 bread">Đặt Phòng Của Tôi</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="ftco-section bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Thông báo đẹp hơn -->
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center justify-content-center mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center justify-content-center mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center justify-content-center mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3>Lịch Sử Đặt Phòng</h3>
                        <div>
                            <a href="{{ route('user.reviews') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i> Đánh giá của tôi
                            </a>
                        </div>
                    </div>
                    
                    @if ($bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đặt phòng</th>
                                        <th>Loại phòng</th>
                                        <th>Ngày nhận</th>
                                        <th>Ngày trả</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Đánh giá</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->booking_id }}</td>
                                            <td>{{ $booking->room->roomType->name }}</td>
                                            <td>{{ $booking->check_in_date->format('d/m/Y') }}</td>
                                            <td>{{ $booking->check_out_date->format('d/m/Y') }}</td>
                                            <td>{{ number_format($booking->price) }}đ</td>
                                            <td>
                                                <span class="badge badge-{{ $booking->status == 'pending' ? 'warning' : ($booking->status == 'confirmed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'primary')) }}">
                                                    {{ $booking->status_text }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $roomType = $booking->room->roomType;
                                                    $hasReviewed = \App\Models\RoomTypeReview::where('user_id', auth()->id())
                                                        ->where('room_type_id', $roomType->id)
                                                        ->exists();
                                                    $canReview = $booking->status === 'completed' && !$hasReviewed;
                                                @endphp
                                                
                                                @if ($hasReviewed)
                                                    @php 
                                                        $review = \App\Models\RoomTypeReview::where('user_id', auth()->id())
                                                            ->where('room_type_id', $roomType->id)
                                                            ->first();
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-warning mr-2">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                                            @endfor
                                                        </div>
                                                        <span class="badge badge-{{ $review->status == 'approved' ? 'success' : ($review->status == 'rejected' ? 'danger' : 'warning') }}">
                                                            {{ $review->status_text }}
                                                        </span>
                                                    </div>
                                                    @if ($review->status === 'pending')
                                                        <div class="mt-1">
                                                            <button class="btn btn-sm btn-outline-primary edit-review-btn" data-review-id="{{ $review->id }}">Sửa</button>
                                                            <button class="btn btn-sm btn-outline-danger delete-review-btn" data-review-id="{{ $review->id }}">Xóa</button>
                                                        </div>
                                                    @endif
                                                @elseif ($canReview)
                                                    <button class="btn btn-sm btn-success create-review-btn" data-room-type-id="{{ $roomType->id }}">
                                                        <i class="fas fa-star"></i> Đánh giá ngay
                                                    </button>
                                                @else
                                                    <span class="text-muted">Chưa thể đánh giá</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($booking->status == 'pending')
                                                    <form action="{{ route('booking.cancel', $booking->id) }}" method="post" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đặt phòng này?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger">Hủy đặt phòng</button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled>{{ $booking->status == 'cancelled' ? 'Đã hủy' : 'Không thể hủy' }}</button>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('booking.detail', $booking->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> Xem chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- <div class="d-flex justify-content-center mt-4">
                            {{ $bookings->links() }}
                        </div> --}}
                    @else
                        <div class="text-center py-5">
                            <p>Bạn chưa có đặt phòng nào.</p>
                            <a href="{{ route('booking') }}" class="btn btn-primary">Đặt phòng ngay</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 