@extends('admin.layouts.admin-master')

@section('title', 'Quản lý thông báo')

@section('header', 'Quản lý thông báo')

@section('content')
<style>
    .sticky-filter-bar {
        position: sticky;
        top: 0;
        z-index: 100;
        background: #fff;
        border-bottom: 1px solid #eee;
        padding: 8px 0 4px 0;
        margin-bottom: 8px;
    }
    .notification-title-small {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .notification-content-main {
        font-size: 1.05rem;
        font-weight: 500;
        color: #222;
    }
    .notification-message-main {
        font-size: 0.98rem;
        color: #555;
    }
    .notification-bulk-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .notification-table th, .notification-table td {
        vertical-align: middle !important;
    }
    .notification-table tbody tr:hover {
        background: #f6f8fa;
    }
    .notification-table .btn {
        min-width: 32px;
        padding: 4px 8px;
    }
</style>
<div class="row">
    <div class="col-12 sticky-filter-bar">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <form id="filterForm" class="d-flex flex-wrap gap-2 mb-0 w-100" style="align-items: center;">
                <input type="text" name="search" id="search" class="form-control form-control-sm" style="max-width: 180px;" placeholder="Tìm kiếm..." value="{{ $search ?? '' }}">
                <select name="type" id="type" class="form-select form-select-sm" style="max-width: 140px;">
                    <option value="">Tất cả loại</option>
                    <option value="booking_created" {{ $type == 'booking_created' ? 'selected' : '' }}>Đặt phòng mới</option>
                    <option value="booking_status_changed" {{ $type == 'booking_status_changed' ? 'selected' : '' }}>Thay đổi trạng thái</option>
                    <option value="booking_cancelled" {{ $type == 'booking_cancelled' ? 'selected' : '' }}>Hủy đặt phòng</option>
                    <option value="booking_note_created" {{ $type == 'booking_note_created' ? 'selected' : '' }}>Ghi chú mới</option>
                    <option value="booking_note_updated" {{ $type == 'booking_note_updated' ? 'selected' : '' }}>Ghi chú cập nhật</option>
                    <option value="booking_note_deleted" {{ $type == 'booking_note_deleted' ? 'selected' : '' }}>Ghi chú xóa</option>
                    <option value="room_type_review_created" {{ $type == 'room_type_review_created' ? 'selected' : '' }}>Đánh giá mới</option>
                    <option value="room_type_review_updated" {{ $type == 'room_type_review_updated' ? 'selected' : '' }}>Đánh giá cập nhật</option>
                    <option value="payment_received" {{ $type == 'payment_received' ? 'selected' : '' }}>Thanh toán</option>
                    <option value="support_ticket" {{ $type == 'support_ticket' ? 'selected' : '' }}>Hỗ trợ</option>
                </select>
                <select name="priority" id="priority" class="form-select form-select-sm" style="max-width: 120px;">
                    <option value="">Tất cả ưu tiên</option>
                    <option value="urgent" {{ $priority == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                    <option value="high" {{ $priority == 'high' ? 'selected' : '' }}>Cao</option>
                    <option value="normal" {{ $priority == 'normal' ? 'selected' : '' }}>Bình thường</option>
                    <option value="low" {{ $priority == 'low' ? 'selected' : '' }}>Thấp</option>
                </select>
                <select name="is_read" id="is_read" class="form-select form-select-sm" style="max-width: 110px;">
                    <option value="">Tất cả trạng thái</option>
                    <option value="0" {{ $isRead === '0' ? 'selected' : '' }}>Chưa đọc</option>
                    <option value="1" {{ $isRead === '1' ? 'selected' : '' }}>Đã đọc</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
            </form>
        </div>
    </div>
    <div class="col-12">
        <div class="notification-title-small"><i class="fas fa-bell me-2"></i>Quản lý thông báo</div>
        <form id="bulkActionForm" method="POST" action="{{ route('admin.notifications.delete-multi') }}">
            @csrf
            <div class="notification-bulk-actions">
                <button type="submit" class="btn btn-danger btn-sm" id="deleteSelectedBtn" disabled>Xóa đã chọn</button>
                <button type="submit" formaction="{{ route('admin.notifications.mark-read-multi') }}" class="btn btn-success btn-sm" id="markReadSelectedBtn" disabled>Đánh dấu đã đọc</button>
            </div>
            <div class="card mt-2">
                <div class="card-body p-0">
                    <div id="notificationsTable">
                        @if($notifications->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 30px;"><input type="checkbox" id="selectAllNotifications"></th>
                                            <th style="width: 50px;" class="d-none d-md-table-cell">#</th>
                                            <th style="width: 60px;">Icon</th>
                                            <th>Nội dung</th>
                                            <th style="width: 100px;" class="d-none d-lg-table-cell">Loại</th>
                                            <th style="width: 80px;" class="d-none d-md-table-cell">Ưu tiên</th>
                                            <th style="width: 80px;" class="d-none d-sm-table-cell">Trạng thái</th>
                                            <th style="width: 120px;" class="d-none d-lg-table-cell">Thời gian</th>
                                            <th style="width: 100px;">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($notifications as $notification)
                                            @php
                                                $link = null;
                                                if (str_contains($notification->type, 'note')) {
                                                    // Ghi chú: chuyển về booking
                                                    $bookingId = $notification->data['booking_id'] ?? null;
                                                    if ($bookingId) {
                                                        $link = route('admin.bookings.show', $bookingId);
                                                    }
                                                } elseif (str_contains($notification->type, 'review')) {
                                                    // Đánh giá: chuyển về trang chi tiết đánh giá
                                                    $reviewId = $notification->data['review_id'] ?? null;
                                                    if ($reviewId) {
                                                        $link = route('admin.room-type-reviews.show', $reviewId);
                                                    }
                                                } elseif (str_contains($notification->type, 'booking')) {
                                                    // Đặt phòng: chuyển về booking
                                                    $bookingId = $notification->data['booking_id'] ?? null;
                                                    if ($bookingId) {
                                                        $link = route('admin.bookings.show', $bookingId);
                                                    }
                                                } elseif (str_contains($notification->type, 'room')) {
                                                    // Phòng: chuyển về trang chi tiết phòng
                                                    $roomId = $notification->data['room_id'] ?? null;
                                                    if ($roomId) {
                                                        $link = route('admin.rooms.show', $roomId);
                                                    }
                                                }
                                            @endphp
                                            <tr class="notification-row" data-id="{{ $notification->id }}">
                                                <td><input type="checkbox" name="notification_id[]" value="{{ $notification->id }}" class="notification-checkbox"></td>
                                                <td class="d-none d-md-table-cell">{{ $notification->id }}</td>
                                                <td>
                                                    @if($link)
                                                        <a href="{{ $link }}" class="text-decoration-none">
                                                    @endif
                                                    <div class="icon-circle bg-{{ $notification->color }}" style="width: 35px; height: 35px;">
                                                        <i class="{{ $notification->display_icon }} text-white" style="font-size: 0.8rem;"></i>
                                                    </div>
                                                    @if($link)
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($link)
                                                        <a href="{{ $link }}" class="notification-content text-decoration-none text-dark">
                                                    @endif
                                                    <div class="notification-content-main fw-bold text-truncate" style="max-width: 350px;" title="{{ $notification->title }}">
                                                        {{ $notification->title }}
                                                    </div>
                                                    <div class="notification-message-main small text-muted text-truncate" style="max-width: 350px;" title="{{ $notification->message }}">
                                                        {{ $notification->message }}
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 mt-1 d-lg-none">
                                                        <span class="badge bg-{{ $notification->color }} small">
                                                            {{ ucfirst($notification->priority) }}
                                                        </span>
                                                        @if($notification->is_read)
                                                            <span class="badge bg-success small">Đã đọc</span>
                                                        @else
                                                            <span class="badge bg-warning small">Chưa đọc</span>
                                                        @endif
                                                        <small class="text-muted">{{ $notification->time_ago }}</small>
                                                    </div>
                                                    @if($link)
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="d-none d-lg-table-cell">
                                                    <span class="badge bg-secondary small">{{ $notification->type }}</span>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <span class="badge bg-{{ $notification->color }} small">
                                                        {{ ucfirst($notification->priority) }}
                                                    </span>
                                                </td>
                                                <td class="d-none d-sm-table-cell">
                                                    @if($notification->is_read)
                                                        <span class="badge bg-success small">Đã đọc</span>
                                                    @else
                                                        <span class="badge bg-warning small">Chưa đọc</span>
                                                    @endif
                                                </td>
                                                <td class="d-none d-lg-table-cell">
                                                    <div class="small text-muted">
                                                        {{ $notification->time_ago }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <form method="POST" action="{{ route('admin.notifications.destroy', $notification->id) }}" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa thông báo này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Xóa"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Phân trang -->
                            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                <div class="small text-muted">
                                    Hiển thị {{ $notifications->firstItem() ?? 0 }} - {{ $notifications->lastItem() ?? 0 }} 
                                    trong tổng số {{ $notifications->total() }} thông báo
                                </div>
                                <div id="paginationContainer">
                                    {{ $notifications->appends(request()->query())->links() }}
                                </div>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-bell-slash"></i>
                                <h5>Không có thông báo</h5>
                                <p>Chưa có thông báo nào phù hợp với bộ lọc hiện tại.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Chỉ giữ lại JS cho hiệu ứng giao diện, bỏ toàn bộ JS xử lý logic AJAX, load bảng, xóa, đánh dấu đã đọc...
$(document).ready(function() {
    // Checkbox chọn tất cả
    $('#selectAllNotifications').on('change', function() {
        $('.notification-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkActionButtons();
    });
    // Checkbox từng dòng
    $('.notification-checkbox').on('change', function() {
        updateBulkActionButtons();
    });
    function updateBulkActionButtons() {
        const anyChecked = $('.notification-checkbox:checked').length > 0;
        $('#deleteSelectedBtn').prop('disabled', !anyChecked);
        $('#markReadSelectedBtn').prop('disabled', !anyChecked);
    }
});
</script>
@endsection 