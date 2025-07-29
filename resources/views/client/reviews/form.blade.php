<form id="reviewFormPopup" method="POST" action="{{ route('room-type-reviews.store-ajax') }}" class="review-form-modern">
    @csrf
    @if(isset($roomType) && $roomType)
        <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
        <div class="alert alert-info border-0 bg-light-info mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-hotel text-info mr-3 fa-lg"></i>
                <div>
                    <strong class="text-dark">Đánh giá cho:</strong> {{ $roomType->name }}
                    @if(isset($booking) && $booking)
                        <br><small class="text-muted">Booking #{{ $booking->booking_id }}</small>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger border-0 mb-4">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Lỗi:</strong> Không tìm thấy thông tin loại phòng.
        </div>
    @endif
    @if(isset($booking) && $booking)
        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
    @endif

    <!-- Rating tổng thể -->
    <div class="form-group mb-4">
        <label class="form-label font-weight-bold text-dark mb-3">
            <i class="fas fa-star text-warning mr-2"></i>Đánh giá tổng thể
            <span class="text-danger">*</span>
        </label>
        <div class="rating-container">
            <div class="rating-stars" data-rating="0">
                @for ($i = 5; $i >= 1; $i--)
                    <input type="radio" name="rating" value="{{ $i }}" id="overall_star{{ $i }}" class="rating-input" required>
                    <label for="overall_star{{ $i }}" class="rating-star" data-value="{{ $i }}">
                        <i class="fas fa-star"></i>
                    </label>
                @endfor
            </div>
            <div class="rating-text mt-2">
                <span class="text-muted">Chọn số sao để đánh giá</span>
            </div>
        </div>
    </div>

    <!-- Đánh giá chi tiết -->
    <div class="detailed-ratings mb-4">
        <h6 class="font-weight-bold text-dark mb-3">
            <i class="fas fa-chart-bar text-primary mr-2"></i>Đánh giá chi tiết
            <small class="text-muted font-weight-normal">(không bắt buộc)</small>
        </h6>
        
        <div class="row">
            <div class="col-md-6">
                <div class="rating-item mb-3">
                    <label class="rating-label">
                        <i class="fas fa-broom text-success mr-2"></i>Vệ sinh
                    </label>
                    <div class="rating-stars-sm" data-rating="0">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" name="cleanliness_rating" value="{{ $i }}" id="cleanliness_star{{ $i }}" class="rating-input-sm">
                            <label for="cleanliness_star{{ $i }}" class="rating-star-sm" data-value="{{ $i }}">
                                <i class="fas fa-star"></i>
                            </label>
                        @endfor
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="rating-item mb-3">
                    <label class="rating-label">
                        <i class="fas fa-couch text-info mr-2"></i>Thoải mái
                    </label>
                    <div class="rating-stars-sm" data-rating="0">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" name="comfort_rating" value="{{ $i }}" id="comfort_star{{ $i }}" class="rating-input-sm">
                            <label for="comfort_star{{ $i }}" class="rating-star-sm" data-value="{{ $i }}">
                                <i class="fas fa-star"></i>
                            </label>
                        @endfor
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="rating-item mb-3">
                    <label class="rating-label">
                        <i class="fas fa-map-marker-alt text-warning mr-2"></i>Vị trí
                    </label>
                    <div class="rating-stars-sm" data-rating="0">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" name="location_rating" value="{{ $i }}" id="location_star{{ $i }}" class="rating-input-sm">
                            <label for="location_star{{ $i }}" class="rating-star-sm" data-value="{{ $i }}">
                                <i class="fas fa-star"></i>
                            </label>
                        @endfor
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="rating-item mb-3">
                    <label class="rating-label">
                        <i class="fas fa-wifi text-primary mr-2"></i>Tiện nghi
                    </label>
                    <div class="rating-stars-sm" data-rating="0">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" name="facilities_rating" value="{{ $i }}" id="facilities_star{{ $i }}" class="rating-input-sm">
                            <label for="facilities_star{{ $i }}" class="rating-star-sm" data-value="{{ $i }}">
                                <i class="fas fa-star"></i>
                            </label>
                        @endfor
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="rating-item mb-3">
                    <label class="rating-label">
                        <i class="fas fa-dollar-sign text-success mr-2"></i>Giá trị
                    </label>
                    <div class="rating-stars-sm" data-rating="0">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" name="value_rating" value="{{ $i }}" id="value_star{{ $i }}" class="rating-input-sm">
                            <label for="value_star{{ $i }}" class="rating-star-sm" data-value="{{ $i }}">
                                <i class="fas fa-star"></i>
                            </label>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bình luận -->
    <div class="form-group mb-4">
        <label for="comment" class="form-label font-weight-bold text-dark mb-2">
            <i class="fas fa-comment text-primary mr-2"></i>Bình luận của bạn
            <span class="text-danger">*</span>
        </label>
        <textarea name="comment" id="comment" class="form-control border-0 bg-light" rows="4" 
                  placeholder="Chia sẻ trải nghiệm của bạn về phòng này... (tối thiểu 10 ký tự)" required></textarea>
        <div class="form-text">
            <small class="text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                Bình luận sẽ giúp khách hàng khác hiểu rõ hơn về chất lượng phòng
            </small>
        </div>
    </div>

    <!-- Tùy chọn ẩn danh -->
    <div class="form-group mb-4">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="is_anonymous" name="is_anonymous" value="1">
            <label class="custom-control-label" for="is_anonymous">
                <i class="fas fa-user-secret text-muted mr-2"></i>
                Đánh giá ẩn danh
            </label>
        </div>
        <small class="form-text text-muted">
            Khi bật, tên của bạn sẽ không hiển thị trong đánh giá
        </small>
    </div>

    <!-- Nút gửi -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm" 
                {{ !isset($roomType) || !$roomType ? 'disabled' : '' }}>
            <i class="fas fa-paper-plane mr-2"></i>
            <span class="btn-text">Gửi đánh giá</span>
            <span class="btn-loading d-none">
                <i class="fas fa-spinner fa-spin mr-2"></i>Đang gửi...
            </span>
        </button>
    </div>
</form>

<style>
.review-form-modern {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.bg-light-info {
    background-color: #e3f2fd !important;
}

.rating-container {
    text-align: center;
}

.rating-stars {
    display: inline-flex;
    flex-direction: row-reverse;
    gap: 8px;
}

.rating-input {
    display: none;
}

.rating-star {
    font-size: 2.5rem;
    color: #e0e0e0;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 4px;
    border-radius: 50%;
}

.rating-star:hover,
.rating-star:hover ~ .rating-star,
.rating-input:checked ~ .rating-star {
    color: #ffc107;
    transform: scale(1.1);
}

.rating-star i {
    transition: all 0.2s ease;
}

.rating-text {
    font-size: 0.9rem;
    min-height: 20px;
}

/* Rating stars nhỏ cho đánh giá chi tiết */
.rating-stars-sm {
    display: inline-flex;
    flex-direction: row-reverse;
    gap: 4px;
}

.rating-input-sm {
    display: none;
}

.rating-star-sm {
    font-size: 1.2rem;
    color: #e0e0e0;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 2px;
}

.rating-star-sm:hover,
.rating-star-sm:hover ~ .rating-star-sm,
.rating-input-sm:checked ~ .rating-star-sm {
    color: #ffc107;
    transform: scale(1.1);
}

.rating-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.rating-item:hover {
    background: #ffffff;
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.1);
}

.rating-label {
    display: block;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.form-control {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.2s ease;
    font-size: 0.95rem;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    background-color: #ffffff;
}

.custom-switch .custom-control-label::before {
    border-radius: 20px;
    height: 24px;
    width: 44px;
}

.custom-switch .custom-control-label::after {
    border-radius: 50%;
    height: 18px;
    width: 18px;
    top: 3px;
    left: 3px;
}

.custom-switch .custom-control-input:checked ~ .custom-control-label::after {
    transform: translateX(20px);
}

.btn-lg {
    padding: 12px 24px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

.btn-loading {
    display: none;
}

.btn-loading.d-none {
    display: none !important;
}

.alert {
    border-radius: 8px;
    border: none;
}

.alert-info {
    background-color: #e3f2fd;
    color: #0c5460;
}

.alert-danger {
    background-color: #ffebee;
    color: #c62828;
}

/* Responsive */
@media (max-width: 768px) {
    .rating-star {
        font-size: 2rem;
    }
    
    .rating-star-sm {
        font-size: 1rem;
    }
    
    .rating-item {
        padding: 12px;
    }
    
    .btn-lg {
        padding: 10px 20px;
        font-size: 1rem;
    }
}
</style>

<script>
$(function() {
    // Rating stars chính
    $('.rating-stars').each(function() {
        const container = $(this);
        const stars = container.find('.rating-star');
        const text = container.siblings('.rating-text');
        const ratingTexts = [
            'Chọn số sao để đánh giá',
            'Rất không hài lòng',
            'Không hài lòng', 
            'Bình thường',
            'Hài lòng',
            'Rất hài lòng'
        ];
        
        stars.on('click', function() {
            const value = $(this).data('value');
            container.attr('data-rating', value);
            text.text(ratingTexts[value]);
            
            // Reset tất cả stars
            stars.find('i').removeClass('text-warning').addClass('text-muted');
            
            // Highlight stars đã chọn
            stars.each(function() {
                const starValue = $(this).data('value');
                if (starValue <= value) {
                    $(this).find('i').removeClass('text-muted').addClass('text-warning');
                }
            });
        });
        
        // Hover effect
        stars.on('mouseenter', function() {
            const value = $(this).data('value');
            stars.find('i').removeClass('text-warning').addClass('text-muted');
            stars.each(function() {
                const starValue = $(this).data('value');
                if (starValue <= value) {
                    $(this).find('i').removeClass('text-muted').addClass('text-warning');
                }
            });
        });
        
        stars.on('mouseleave', function() {
            const currentRating = container.attr('data-rating') || 0;
            stars.find('i').removeClass('text-warning').addClass('text-muted');
            stars.each(function() {
                const starValue = $(this).data('value');
                if (starValue <= currentRating) {
                    $(this).find('i').removeClass('text-muted').addClass('text-warning');
                }
            });
        });
    });
    
    // Rating stars nhỏ
    $('.rating-stars-sm').each(function() {
        const container = $(this);
        const stars = container.find('.rating-star-sm');
        
        stars.on('click', function() {
            const value = $(this).data('value');
            container.attr('data-rating', value);
            
            // Reset tất cả stars
            stars.find('i').removeClass('text-warning').addClass('text-muted');
            
            // Highlight stars đã chọn
            stars.each(function() {
                const starValue = $(this).data('value');
                if (starValue <= value) {
                    $(this).find('i').removeClass('text-muted').addClass('text-warning');
                }
            });
        });
        
        // Hover effect
        stars.on('mouseenter', function() {
            const value = $(this).data('value');
            stars.find('i').removeClass('text-warning').addClass('text-muted');
            stars.each(function() {
                const starValue = $(this).data('value');
                if (starValue <= value) {
                    $(this).find('i').removeClass('text-muted').addClass('text-warning');
                }
            });
        });
        
        stars.on('mouseleave', function() {
            const currentRating = container.attr('data-rating') || 0;
            stars.find('i').removeClass('text-warning').addClass('text-muted');
            stars.each(function() {
                const starValue = $(this).data('value');
                if (starValue <= currentRating) {
                    $(this).find('i').removeClass('text-muted').addClass('text-warning');
                }
            });
        });
    });
    
    // Form submission
    $('#reviewFormPopup').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const btn = form.find('button[type=submit]');
        const btnText = btn.find('.btn-text');
        const btnLoading = btn.find('.btn-loading');
        
        // Kiểm tra rating tổng thể
        const overallRating = form.find('.rating-stars').attr('data-rating');
        if (!overallRating || overallRating == '0') {
            showToast('Vui lòng chọn đánh giá tổng thể!', 'warning');
            return;
        }
        
        // Kiểm tra comment
        const comment = form.find('#comment').val().trim();
        if (comment.length < 10) {
            showToast('Bình luận phải có ít nhất 10 ký tự!', 'warning');
            return;
        }
        
        // Disable button và hiển thị loading
        btn.prop('disabled', true);
        btnText.addClass('d-none');
        btnLoading.removeClass('d-none');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(res) {
                btn.prop('disabled', false);
                btnText.removeClass('d-none');
                btnLoading.addClass('d-none');
                
                $('#reviewFormModal').modal('hide');
                
                // Reload data nếu có function
                if (typeof loadReviewsData === 'function') loadReviewsData();
                if (typeof loadBookingsData === 'function') loadBookingsData();
                
                showToast(res.message || 'Đánh giá đã được gửi thành công!', 'success');
                
                // Reset form
                setTimeout(() => {
                    form[0].reset();
                    $('.rating-stars').attr('data-rating', '0');
                    $('.rating-stars-sm').attr('data-rating', '0');
                    $('.rating-star i, .rating-star-sm i').removeClass('text-warning').addClass('text-muted');
                    $('.rating-text').text('Chọn số sao để đánh giá');
                }, 1000);
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                btnText.removeClass('d-none');
                btnLoading.addClass('d-none');
                
                let msg = 'Có lỗi xảy ra khi gửi đánh giá.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showToast(msg, 'danger');
            }
        });
    });
    
    // Character counter cho comment
    $('#comment').on('input', function() {
        const length = $(this).val().length;
        const minLength = 10;
        const maxLength = 1000;
        
        if (length < minLength) {
            $(this).addClass('is-invalid').removeClass('is-valid');
        } else if (length > maxLength) {
            $(this).addClass('is-invalid').removeClass('is-valid');
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    });
});

// Toast notification function
function showToast(message, type = 'info') {
    const toast = $(`
        <div class="toast-notification toast-notification-${type}" role="alert">
            <div class="toast-header">
                <i class="fas fa-${type === 'success' ? 'check-circle text-success' : type === 'warning' ? 'exclamation-triangle text-warning' : type === 'danger' ? 'times-circle text-danger' : 'info-circle text-info'} mr-2"></i>
                <strong class="mr-auto">Thông báo</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                    <span>&times;</span>
                </button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `);
    
    // Thêm toast vào container
    if ($('#toast-container').length === 0) {
        $('body').append('<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>');
    }
    
    $('#toast-container').append(toast);
    toast.toast({ delay: 5000 }).toast('show');
    
    // Tự động xóa sau khi ẩn
    toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}
</script>

<style>
/* Toast styles */
.toast-notification {
    min-width: 300px;
    margin-bottom: 10px;
}

.toast-notification-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.toast-notification-warning {
    background-color: #fff3cd;
    border-color: #ffeaa7;
}

.toast-notification-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.toast-notification-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
}
</style> 