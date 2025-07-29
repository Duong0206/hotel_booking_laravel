@extends(auth()->user()->role && auth()->user()->role->name === 'admin' ? 'admin.layouts.admin-master' : 'client.layouts.master')

@section('title', 'Ghi chú đặt phòng')

@if(auth()->user()->role && auth()->user()->role->name === 'admin')
@section('header', 'Ghi chú đặt phòng')
@endif

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-sticky-note"></i>
                        Ghi chú đặt phòng #{{ $bookingId }}
                    </h4>
                    <div>
                        @if(auth()->user()->role && in_array(auth()->user()->role->name, ['admin', 'staff']))
                            <a href="{{ route('booking-notes.create', $bookingId) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Thêm ghi chú
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tìm kiếm -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form action="{{ route('booking-notes.search', $bookingId) }}" method="GET" class="d-flex">
                                <input type="text" name="keyword" class="form-control me-2" 
                                       placeholder="Tìm kiếm ghi chú..." 
                                       value="{{ $keyword ?? '' }}">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('booking-notes.index', $bookingId) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-refresh"></i> Làm mới
                            </a>
                        </div>
                    </div>

                    <!-- Thông báo -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Danh sách ghi chú -->
                    @if($notesWithPermissions->count() > 0)
                        @foreach($notesWithPermissions as $note)
                            <div class="card mb-3 {{ $note->is_internal ? 'border-warning' : '' }}">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-{{ $note->type === 'admin' ? 'danger' : ($note->type === 'staff' ? 'warning' : 'info') }} me-2">
                                            {{ $note->type_text }}
                                        </span>
                                        <span class="badge bg-{{ $note->visibility === 'private' ? 'secondary' : ($note->visibility === 'internal' ? 'warning' : 'primary') }} me-2">
                                            {{ $note->visibility_text }}
                                        </span>
                                        @if($note->is_internal)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-eye-slash me-1"></i>Nội bộ
                                            </span>
                                        @endif
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        @if($note->can_edit)
                                            <a href="{{ route('booking-notes.edit', [$bookingId, $note->id]) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($note->can_delete)
                                            <form action="{{ route('booking-notes.destroy', [$bookingId, $note->id]) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa ghi chú này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="note-content">
                                        {!! nl2br(e($note->content)) !!}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>{{ $note->user->name }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $note->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-sticky-note fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có ghi chú nào</h5>
                            <p class="text-muted">Hãy tạo ghi chú đầu tiên cho đặt phòng này</p>
                            @if(auth()->user()->role && in_array(auth()->user()->role->name, ['admin', 'staff']))
                                <a href="{{ route('booking-notes.create', $bookingId) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Thêm ghi chú
                                </a>
                            @endif
                        </div>
                    @endif
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
}

.card.border-warning {
    border-color: #ffc107 !important;
}

.card.border-warning .card-header {
    background-color: #fff3cd;
}
</style>
@endsection 