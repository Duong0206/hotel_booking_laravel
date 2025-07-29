@extends('admin.layouts.admin-master')

@section('title', 'Tạo đánh giá loại phòng mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.room-type-reviews.index') }}">Quản lý đánh giá loại phòng</a></li>
                            <li class="breadcrumb-item active">Tạo đánh giá mới</li>
                        </ol>
                    </nav>
                </div>
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4><i class="fas fa-plus"></i> Tạo đánh giá loại phòng mới</h4>
                        <a href="{{ route('admin.room-type-reviews.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.room-type-reviews.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id" class="form-label">Người đánh giá <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                        <option value="">Chọn người đánh giá</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_type_id" class="form-label">Loại phòng <span class="text-danger">*</span></label>
                                    <select name="room_type_id" id="room_type_id" class="form-select @error('room_type_id') is-invalid @enderror" required>
                                        <option value="">Chọn loại phòng</option>
                                        @foreach($roomTypes as $roomType)
                                            <option value="{{ $roomType->id }}" {{ old('room_type_id') == $roomType->id ? 'selected' : '' }}>
                                                {{ $roomType->name }} - {{ number_format($roomType->price) }}đ/đêm
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('room_type_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="rating" class="form-label">Điểm đánh giá <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                                    <label for="star{{ $i }}" class="star-label">
                                        <i class="fas fa-star"></i>
                                    </label>
                                @endfor
                            </div>
                            <div class="rating-labels mt-2">
                                <small class="text-muted">
                                    <span class="mr-3">1 - Rất không hài lòng</span>
                                    <span class="mr-3">2 - Không hài lòng</span>
                                    <span class="mr-3">3 - Bình thường</span>
                                    <span class="mr-3">4 - Hài lòng</span>
                                    <span>5 - Rất hài lòng</span>
                                </small>
                            </div>
                            @error('rating')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="comment" class="form-label">Bình luận</label>
                            <textarea name="comment" id="comment" rows="5" class="form-control @error('comment') is-invalid @enderror" placeholder="Nội dung đánh giá...">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Tối đa 1000 ký tự</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" id="is_anonymous" name="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_anonymous">
                                            <i class="fas fa-user-secret"></i> Đánh giá ẩn danh
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Nếu chọn, tên người đánh giá sẽ không hiển thị công khai</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Tạo đánh giá
                            </button>
                            <a href="{{ route('admin.room-type-reviews.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 5px;
}

.rating-input input[type="radio"] {
    display: none;
}

.star-label {
    font-size: 2.5rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s ease;
}

.star-label:hover,
.star-label:hover ~ .star-label,
.rating-input input[type="radio"]:checked ~ .star-label {
    color: #ffc107;
}

.rating-input:hover .star-label {
    color: #ddd;
}

.rating-input:hover .star-label:hover,
.rating-input:hover .star-label:hover ~ .star-label {
    color: #ffc107;
}

.rating-labels {
    font-size: 0.85rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const starLabels = document.querySelectorAll('.star-label');
    
    // Hàm cập nhật màu sao
    function updateStars(selectedIndex) {
        starLabels.forEach((label, index) => {
            if (index <= selectedIndex) {
                label.style.color = '#ffc107';
            } else {
                label.style.color = '#ddd';
            }
        });
    }
    
    // Xử lý sự kiện click vào sao
    starLabels.forEach((label, index) => {
        label.addEventListener('click', function() {
            const radio = document.getElementById(`star${5 - index}`);
            radio.checked = true;
            updateStars(index);
        });
    });
    
    // Xử lý sự kiện hover
    starLabels.forEach((label, index) => {
        label.addEventListener('mouseenter', function() {
            updateStars(index);
        });
    });
    
    // Reset màu khi rời chuột khỏi container
    document.querySelector('.rating-input').addEventListener('mouseleave', function() {
        const checkedInput = document.querySelector('input[name="rating"]:checked');
        if (checkedInput) {
            const index = Array.from(ratingInputs).indexOf(checkedInput);
            updateStars(index);
        } else {
            starLabels.forEach(label => {
                label.style.color = '#ddd';
            });
        }
    });
    
    // Set initial rating if exists
    const checkedInput = document.querySelector('input[name="rating"]:checked');
    if (checkedInput) {
        const index = Array.from(ratingInputs).indexOf(checkedInput);
        updateStars(index);
    }
});
</script>
@endsection 