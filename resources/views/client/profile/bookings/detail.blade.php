<div class="booking-detail">
    <div class="row">
        <div class="col-md-6">
            <h6 class="text-info mb-3"><i class="fas fa-calendar-check mr-2"></i>Thông Tin Đặt Phòng</h6>
            <table class="table table-borderless">
                <tr>
                    <td><strong>Mã đặt phòng:</strong></td>
                    <td>{{ $booking->booking_id }}</td>
                </tr>
                                <tr>
                    <td><strong>Loại phòng:</strong></td>
                    <td>{{ $booking->room->roomType->name }}</td>
                </tr>
                <tr>
                    <td><strong>Ngày check-in:</strong></td>
                    <td>{{ $booking->check_in_date->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Ngày check-out:</strong></td>
                    <td>{{ $booking->check_out_date->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Số đêm:</strong></td>
                    <td>{{ $booking->check_in_date->diffInDays($booking->check_out_date) }} đêm</td>
                </tr>
                <tr>
                    <td><strong>Giá:</strong></td>
                    <td class="text-primary font-weight-bold">{{ number_format($booking->price) }}đ</td>
                </tr>
                <tr>
                    <td><strong>Trạng thái:</strong></td>
                    <td>
                        <span class="badge badge-{{ $booking->status == 'pending' ? 'warning' : ($booking->status == 'confirmed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'primary')) }}">
                            {{ $booking->status_text }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="text-info mb-3"><i class="fas fa-user mr-2"></i>Thông Tin Khách Hàng</h6>
            <table class="table table-borderless">
                <tr>
                    <td><strong>Họ tên:</strong></td>
                    <td>{{ $booking->user->name }}</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td>{{ $booking->user->email }}</td>
                </tr>
                <tr>
                    <td><strong>Số điện thoại:</strong></td>
                    <td>{{ $booking->phone ?? 'Chưa cung cấp' }}</td>
                </tr>
                <tr>
                    <td><strong>Ghi chú:</strong></td>
                    <td>{{ $booking->special_requests ?? 'Không có' }}</td>
                </tr>
                <tr>
                    <td><strong>Ngày đặt:</strong></td>
                    <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    @php
        $roomType = $booking->room->roomType;
        $hasReviewed = \App\Models\RoomTypeReview::where('user_id', auth()->id())
            ->where('room_type_id', $roomType->id)
            ->exists();
    @endphp
    
    @if($hasReviewed)
        <hr>
        <div class="row">
            <div class="col-12">
                <h6 class="text-warning mb-3"><i class="fas fa-star mr-2"></i>Đánh Giá</h6>
                @php 
                    $review = \App\Models\RoomTypeReview::where('user_id', auth()->id())
                        ->where('room_type_id', $roomType->id)
                        ->first();
                @endphp
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="text-warning">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                @endfor
                                <span class="ml-2">{{ $review->rating }}/5</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-{{ $review->status == 'approved' ? 'success' : ($review->status == 'rejected' ? 'danger' : 'warning') }} mr-2">
                                    {{ $review->status_text }}
                                </span>
                                @if ($review->status === 'pending')
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary edit-review-btn" data-review-id="{{ $review->id }}" title="Chỉnh sửa đánh giá">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                        <button type="button" class="btn btn-outline-danger delete-review-btn" data-review-id="{{ $review->id }}" title="Xóa đánh giá">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="mb-0">{{ $review->comment }}</p>
                        @else
                            <p class="text-muted mb-0"><em>Không có bình luận</em></p>
                        @endif
                        <small class="text-muted">Đánh giá ngày: {{ $review->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
    @else
        <hr>
        <div class="row">
            <div class="col-12">
                <h6 class="text-warning mb-3"><i class="fas fa-star mr-2"></i>Đánh Giá</h6>
                @if ($booking->status == 'completed')
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Bạn có thể đánh giá loại phòng này để giúp chúng tôi cải thiện dịch vụ.
                        <button class="btn btn-sm btn-primary ml-2 create-review-btn" data-room-type-id="{{ $roomType->id }}">
                            <i class="fas fa-star"></i> Viết đánh giá
                        </button>
                    </div>
                @else
                    <div class="alert alert-secondary">
                        <i class="fas fa-clock mr-2"></i>
                        Bạn chỉ có thể đánh giá sau khi hoàn thành đặt phòng.
                    </div>
                @endif
            </div>
        </div>
        {{-- @if ($booking->status == 'completed' && !$hasReviewed)
            <button class="btn btn-sm btn-success create-review-btn" data-room-type-id="{{ $roomType->id }}">Đánh giá</button>
        @endif --}}
    @endif

    <!-- Ghi chú đặt phòng -->
    <div class="mt-4">
        <x-booking-notes :booking="$booking" :showAddButton="true" :showSearch="true" />
    </div>
</div> 