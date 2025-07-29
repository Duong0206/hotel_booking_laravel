@if ($bookings->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Mã đặt phòng</th>
                    <th>Phòng</th>
                    <th>Ngày check-in</th>
                    <th>Ngày check-out</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                    <th>Đánh giá</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $booking)
                    @php
                        $roomType = $booking->room->roomType;
                        $review = \App\Models\RoomTypeReview::where('user_id', auth()->id())
                            ->where('booking_id', $booking->id)
                            ->first();
                        $hasReviewed = !!$review;
                        $canReview = $booking->status === 'completed' && !$hasReviewed;
                    @endphp
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
                            @if ($hasReviewed)
                                <span class="badge badge-{{ $review->status == 'approved' ? 'success' : ($review->status == 'rejected' ? 'danger' : 'warning') }}">
                                    {{ $review->status == 'approved' ? 'Đã duyệt' : ($review->status == 'rejected' ? 'Bị từ chối' : 'Chờ duyệt') }}
                                </span>
                                <br>
                                <i class="fas fa-star"></i> {{ $review->rating }}/5
                                @if ($review->status === 'rejected')
                                    <div class="text-danger small mt-1">Đánh giá bị từ chối. Bạn có thể gửi lại.</div>
                                @endif
                            @elseif ($canReview)
                                <button class="btn btn-sm btn-success create-review-btn" data-room-type-id="{{ $roomType->id }}" data-booking-id="{{ $booking->id }}">Đánh giá</button>
                            @else
                                <span class="text-muted">Không thể đánh giá</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-info btn-view-booking" data-booking-id="{{ $booking->id }}">Chi tiết</button>
                            @if ($booking->status == 'pending')
                                <button class="btn btn-sm btn-outline-danger cancel-booking-btn" data-booking-id="{{ $booking->id }}">Hủy</button>
                            @elseif ($booking->status == 'cancelled')
                                <button class="btn btn-sm btn-secondary" disabled>Đã hủy</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Phân trang -->
        @if($bookings->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@else
    <div class="text-center py-4">
        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">Chưa có đặt phòng nào</h5>
        <p class="text-muted">Bạn chưa có lịch sử đặt phòng nào.</p>
        <a href="{{ route('rooms') }}" class="btn btn-primary">
            <i class="fas fa-search"></i> Tìm phòng ngay
        </a>
    </div>
@endif 