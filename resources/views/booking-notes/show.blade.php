@extends(auth()->user()->role && auth()->user()->role->name === 'admin' ? 'admin.layouts.admin-master' : 'client.layouts.master')

@section('title', 'Chi tiết ghi chú')

@if(auth()->user()->role && auth()->user()->role->name === 'admin')
@section('header', 'Chi tiết ghi chú')
@endif

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-eye"></i>
                        Chi tiết ghi chú
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Thông tin ghi chú -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Loại ghi chú:</strong>
                                <span class="badge bg-{{ $note->type === 'admin' ? 'danger' : ($note->type === 'staff' ? 'warning' : 'info') }} ms-2">
                                    {{ $note->type === 'customer' ? 'Khách hàng' : ($note->type === 'staff' ? 'Nhân viên' : 'Quản trị') }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Quyền xem:</strong>
                                <span class="badge bg-{{ $note->visibility === 'private' ? 'secondary' : ($note->visibility === 'internal' ? 'warning' : 'primary') }} ms-2">
                                    {{ $note->visibility === 'public' ? 'Công khai' : ($note->visibility === 'private' ? 'Riêng tư' : 'Nội bộ') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Tạo bởi:</strong> {{ $note->user->name }}
                            </div>
                            <div class="mb-3">
                                <strong>Ngày tạo:</strong> {{ $note->created_at->format('d/m/Y H:i') }}
                            </div>
                            @if($note->updated_at != $note->created_at)
                                <div class="mb-3">
                                    <strong>Cập nhật lần cuối:</strong> {{ $note->updated_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($note->is_internal)
                        <div class="alert alert-warning">
                            <i class="fas fa-eye-slash me-2"></i>
                            <strong>Ghi chú nội bộ:</strong> Chỉ admin mới có thể xem ghi chú này.
                        </div>
                    @endif

                    <!-- Nội dung ghi chú -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Nội dung ghi chú</h5>
                        </div>
                        <div class="card-body">
                            <div class="note-content">
                                {!! nl2br(e($note->content)) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin booking -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin đặt phòng</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Mã đặt phòng:</strong> #{{ $note->booking->id }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Trạng thái:</strong> 
                                    <span class="badge bg-{{ $note->booking->status === 'confirmed' ? 'success' : ($note->booking->status === 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ $note->booking->status === 'confirmed' ? 'Đã xác nhận' : ($note->booking->status === 'cancelled' ? 'Đã hủy' : 'Chờ xác nhận') }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>Check-in:</strong> {{ $note->booking->check_in_date->format('d/m/Y') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Check-out:</strong> {{ $note->booking->check_out_date->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nút điều khiển -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('booking-notes.index', $note->booking_id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại danh sách
                        </a>
                        <div>
                            @if($note->can_edit)
                                <a href="{{ route('booking-notes.edit', [$note->booking_id, $note->id]) }}" class="btn btn-primary me-2">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </a>
                            @endif
                            @if($note->can_delete)
                                <form action="{{ route('booking-notes.destroy', [$note->booking_id, $note->id]) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa ghi chú này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.note-content {
    white-space: pre-wrap;
    word-wrap: break-word;
    line-height: 1.6;
    font-size: 1rem;
    color: #333;
}
</style>
@endsection 