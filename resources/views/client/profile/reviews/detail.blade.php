<div class="review-detail">
    <div class="row">
        <div class="col-md-6">
            <h6 class="text-warning mb-3"><i class="fas fa-star mr-2"></i>Thông Tin Đánh Giá</h6>
            <table class="table table-borderless">
                <tr>
                    <td><strong>Điểm đánh giá:</strong></td>
                    <td>
                        <div class="text-warning">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                            @endfor
                            <span class="ml-2">{{ $review->rating }}/5</span>
                        </div>
                    </td>
                </tr>
                @if($review->cleanliness_rating || $review->comfort_rating || $review->location_rating || $review->facilities_rating || $review->value_rating)
                    <tr>
                        <td><strong>Đánh giá chi tiết:</strong></td>
                        <td>
                            <div class="row">
                                @if($review->cleanliness_rating)
                                    <div class="col-md-6 mb-2">
                                        <small><strong>Vệ sinh:</strong> {{ $review->cleanliness_rating }}/5</small>
                                    </div>
                                @endif
                                @if($review->comfort_rating)
                                    <div class="col-md-6 mb-2">
                                        <small><strong>Tiện nghi:</strong> {{ $review->comfort_rating }}/5</small>
                                    </div>
                                @endif
                                @if($review->location_rating)
                                    <div class="col-md-6 mb-2">
                                        <small><strong>Vị trí:</strong> {{ $review->location_rating }}/5</small>
                                    </div>
                                @endif
                                @if($review->facilities_rating)
                                    <div class="col-md-6 mb-2">
                                        <small><strong>Cơ sở vật chất:</strong> {{ $review->facilities_rating }}/5</small>
                                    </div>
                                @endif
                                @if($review->value_rating)
                                    <div class="col-md-6 mb-2">
                                        <small><strong>Giá trị:</strong> {{ $review->value_rating }}/5</small>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Trạng thái:</strong></td>
                    <td>
                        <span class="badge badge-{{ $review->status == 'approved' ? 'success' : ($review->status == 'rejected' ? 'danger' : 'warning') }}">
                            {{ $review->status_text }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Ngày đánh giá:</strong></td>
                    <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td><strong>Cập nhật lần cuối:</strong></td>
                    <td>{{ $review->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="text-warning mb-3"><i class="fas fa-bed mr-2"></i>Thông Tin Loại Phòng</h6>
            <table class="table table-borderless">
                <tr>
                    <td><strong>Loại phòng:</strong></td>
                    <td>{{ $review->roomType->name }}</td>
                </tr>
                <tr>
                    <td><strong>Mô tả:</strong></td>
                    <td>{{ $review->roomType->description ?? 'Không có mô tả' }}</td>
                </tr>
                <tr>
                    <td><strong>Giá:</strong></td>
                    <td>{{ number_format($review->roomType->price) }}đ/đêm</td>
                </tr>
            </table>
        </div>
    </div>
    
    <hr>
    <div class="row">
        <div class="col-12">
            <h6 class="text-warning mb-3"><i class="fas fa-comment mr-2"></i>Nội Dung Đánh Giá</h6>
            @if($review->comment)
                <div class="card">
                    <div class="card-body">
                        <p class="mb-0">{{ $review->comment }}</p>
                    </div>
                </div>
            @else
                <div class="alert alert-secondary">
                    <i class="fas fa-info-circle mr-2"></i>
                    Không có bình luận
                </div>
            @endif
        </div>
    </div>
    
    @if($review->status == 'pending')
        <hr>
        <div class="row">
            <div class="col-12">
                <h6 class="text-info mb-3"><i class="fas fa-edit mr-2"></i>Hành Động</h6>
                <div class="btn-group" role="group">
                    <button class="btn btn-warning edit-review-btn" data-review-id="{{ $review->id }}">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </button>
                    <button class="btn btn-danger delete-review-btn" data-review-id="{{ $review->id }}">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>
            </div>
        </div>
    @endif
</div> 