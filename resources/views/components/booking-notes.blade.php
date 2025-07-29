@props(['booking', 'showAddButton' => true, 'showSearch' => true])

@php
    // Lấy ghi chú từ service với phân trang
    $bookingNoteService = app(\App\Interfaces\Services\BookingServiceInterface::class);
    $notes = $bookingNoteService->getPaginatedNotes($booking->id, 10);
    
    // Thêm thông tin quyền cho mỗi ghi chú
    $notesWithPermissions = $notes->getCollection()->map(function ($note) use ($bookingNoteService) {
        $note->can_edit = $bookingNoteService->canEditNote($note->id);
        $note->can_delete = $bookingNoteService->canDeleteNote($note->id);
        $note->type_text = match($note->type) {
            'customer' => 'Khách hàng',
            'staff' => 'Nhân viên', 
            'admin' => 'Quản lý',
            'system' => 'Hệ thống',
            default => 'Không xác định'
        };
        $note->visibility_text = match($note->visibility) {
            'public' => 'Công khai',
            'private' => 'Riêng tư',
            'internal' => 'Nội bộ',
            default => 'Không xác định'
        };
        return $note;
    });
    
    // Gán lại collection đã xử lý
    $notes->setCollection($notesWithPermissions);
    
    $user = auth()->user();
    $isCustomer = $user && $user->role && $user->role->name === 'customer';
    $isAdmin = $user && $user->role && $user->role->name === 'admin';
    $isStaff = $user && $user->role && $user->role->name === 'staff';
@endphp

<div class="booking-notes-section">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-comments me-2"></i>
                Ghi chú đặt phòng
                <span class="badge bg-primary ms-2">{{ $notes->total() }}</span>
            </h5>
            
            @if($showAddButton)
                @if($isCustomer)
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#customerRequestModal">
                        <i class="fas fa-plus me-1"></i>Gửi yêu cầu
                    </button>
                @elseif($isAdmin || $isStaff)
                    <a href="{{ route('booking-notes.create', $booking->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Thêm ghi chú
                    </a>
                @endif
            @endif
        </div>
        
        <div class="card-body">
            @if($showSearch)
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="noteSearch" placeholder="Tìm kiếm ghi chú...">
                        <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            @endif
            
            <div id="notesContainer">
                @if($notes->count() > 0)
                    @foreach($notes as $note)
                        <div class="note-item border rounded p-3 mb-3 {{ $note->visibility === 'internal' ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>{{ $note->user->name }}</strong>
                                    <span class="badge bg-{{ $note->type === 'admin' ? 'danger' : ($note->type === 'staff' ? 'warning' : ($note->type === 'system' ? 'secondary' : 'info')) }} ms-2">
                                        {{ $note->type_text }}
                                    </span>
                                    <span class="badge bg-{{ $note->visibility === 'public' ? 'success' : ($note->visibility === 'private' ? 'secondary' : 'warning') }} ms-1">
                                        {{ $note->visibility_text }}
                                    </span>
                                    @if($note->is_internal)
                                        <span class="badge bg-danger ms-1">Nội bộ</span>
                                    @endif
                                </div>
                                
                                @if($note->can_edit || $note->can_delete)
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($note->can_edit)
                                                <li><a class="dropdown-item" href="{{ route('booking-notes.edit', [$booking->id, $note->id]) }}">
                                                    <i class="fas fa-edit me-2"></i>Chỉnh sửa
                                                </a></li>
                                            @endif
                                            @if($note->can_delete)
                                                <li>
                                                    <form action="{{ route('booking-notes.destroy', [$booking->id, $note->id]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Bạn có chắc muốn xóa ghi chú này?')">
                                                            <i class="fas fa-trash me-2"></i>Xóa
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="note-content">
                                {!! nl2br(e($note->content)) !!}
                            </div>
                            
                            <div class="note-meta text-muted small mt-2">
                                <i class="fas fa-clock me-1"></i>{{ $note->created_at->format('d/m/Y H:i') }}
                                @if($note->updated_at != $note->created_at)
                                    <span class="ms-2">
                                        <i class="fas fa-edit me-1"></i>Cập nhật: {{ $note->updated_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    
                    @if($notes->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $notes->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-comment-slash fa-2x mb-2"></i>
                        <p>Chưa có ghi chú nào</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal gửi yêu cầu cho customer -->
@if($isCustomer)
<div class="modal fade" id="customerRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('booking-notes.store-request', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customerRequestContent" class="form-label">Nội dung yêu cầu</label>
                        <textarea name="content" id="customerRequestContent" class="form-control" rows="4" required 
                                  placeholder="Nhập yêu cầu của bạn..."></textarea>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Yêu cầu của bạn sẽ được gửi đến admin để xử lý
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>Gửi yêu cầu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
.note-item {
    transition: all 0.3s ease;
}

.note-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.note-content {
    line-height: 1.6;
    white-space: pre-wrap;
}

.badge {
    font-size: 0.75em;
}
</style> 