@extends('client.layouts.master')

@section('title', $promotion->title)

@section('content')
<div class="hero-wrap" style="background-image: url('{{ asset('client/images/bg_1.jpg') }}');">
    <div class="overlay"></div>
    <div class="container">
        <div class="row no-gutters slider-text d-flex align-items-end justify-content-center">
            <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
                <div class="text">
                    <p class="breadcrumbs mb-2">
                        <span class="mr-2"><a href="{{ route('index') }}">Trang chủ</a></span> 
                        <span class="mr-2"><a href="{{ route('promotions.index') }}">Khuyến mại</a></span>
                        <span>{{ $promotion->title }}</span>
                    </p>
                    <h1 class="mb-4 bread">Chi Tiết Khuyến Mại</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="ftco-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="promotion-detail">
                    {{-- Hình ảnh khuyến mại --}}
                    @if($promotion->image)
                        <div class="promotion-image mb-4">
                            <img src="{{ asset('storage/' . $promotion->image) }}" class="img-fluid rounded" alt="{{ $promotion->title }}">
                        </div>
                    @else
                        <div class="promotion-image mb-4">
                            <div class="bg-gradient-primary text-white text-center py-5 rounded">
                                <i class="fas fa-percentage fa-4x mb-3"></i>
                                <h2>{{ $promotion->discount_text }}</h2>
                                <p class="mb-0">Giảm giá đặc biệt</p>
                            </div>
                        </div>
                    @endif

                    {{-- Thông tin khuyến mại --}}
                    <div class="promotion-info bg-light p-4 rounded mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="mb-3">{{ $promotion->title }}</h3>
                                <div class="promotion-details">
                                    <div class="detail-item mb-2">
                                        <strong><i class="fas fa-tag text-primary"></i> Mã khuyến mại:</strong>
                                        <span class="badge badge-primary ml-2">{{ $promotion->code }}</span>
                                        <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copyToClipboard('{{ $promotion->code }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="detail-item mb-2">
                                        <strong><i class="fas fa-percentage text-success"></i> Giảm giá:</strong>
                                        <span class="text-success">{{ $promotion->discount_text }}</span>
                                    </div>
                                    <div class="detail-item mb-2">
                                        <strong><i class="fas fa-calendar-alt text-warning"></i> Hạn sử dụng:</strong>
                                        <span>Đến ngày {{ $promotion->expired_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="detail-item mb-2">
                                        <strong><i class="fas fa-building text-info"></i> Phạm vi áp dụng:</strong>
                                        <div class="mt-2">
                                            <p class="mb-1">{{ $promotion->apply_scope_text }}</p>
                                            @if($promotion->apply_scope_details)
                                                <div class="apply-scope-details small">
                                                    @foreach($promotion->apply_scope_details as $detail)
                                                        <div class="mb-2">
                                                            <strong>{{ $detail['name'] }}</strong>
                                                            @if(isset($detail['count']))
                                                                <span class="text-muted">({{ $detail['count'] }})</span>
                                                            @endif
                                                            @if(isset($detail['rooms']))
                                                                <div class="rooms-list">
                                                                    Phòng: {{ implode(', ', $detail['rooms']) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($promotion->minimum_amount > 0)
                                        <div class="detail-item mb-2">
                                            <strong><i class="fas fa-money-bill text-info"></i> Đơn tối thiểu:</strong>
                                            <span>{{ number_format($promotion->minimum_amount, 0, ',', '.') }}đ</span>
                                        </div>
                                    @endif
                                    @if($promotion->usage_limit)
                                        <div class="detail-item mb-2">
                                            <strong><i class="fas fa-users text-secondary"></i> Còn lại:</strong>
                                            <span>{{ $promotion->usage_limit - $promotion->used_count }} lượt</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="status-badge text-center">
                                    <span class="badge badge-lg badge-success">{{ $promotion->status }}</span>
                                    <div class="mt-3">
                                        <a href="{{ route('booking') }}" class="btn btn-primary btn-lg">
                                            <i class="fas fa-hotel"></i> Đặt phòng ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mô tả chi tiết --}}
                    <div class="promotion-description mb-4">
                        <h4 class="mb-3">Mô tả khuyến mại</h4>
                        <div class="content">
                            {!! nl2br(e($promotion->description)) !!}
                        </div>
                    </div>

                    {{-- Điều khoản và điều kiện --}}
                    @if($promotion->terms_conditions)
                        <div class="terms-conditions">
                            <h4 class="mb-3">Điều khoản và điều kiện</h4>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Lưu ý quan trọng:</strong>
                            </div>
                            <div class="content">
                                {!! nl2br(e($promotion->terms_conditions)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="sidebar">
                    {{-- Thông tin nhanh --}}
                    <div class="sidebar-box bg-primary text-white p-4 rounded mb-4">
                        <h5 class="mb-3"><i class="fas fa-info-circle"></i> Thông tin nhanh</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-tag"></i> 
                                <strong>Mã:</strong> {{ $promotion->code }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-percentage"></i> 
                                <strong>Giảm:</strong> {{ $promotion->discount_text }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-calendar"></i> 
                                <strong>Hết hạn:</strong> {{ $promotion->expired_at->format('d/m/Y') }}
                            </li>
                            @if($promotion->minimum_amount > 0)
                            <li class="mb-2">
                                <i class="fas fa-money-bill"></i> 
                                <strong>Tối thiểu:</strong> {{ number_format($promotion->minimum_amount, 0, ',', '.') }}đ
                            </li>
                            @endif
                        </ul>
                        <div class="text-center mt-3">
                            <a href="{{ route('booking') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-hotel text-primary"></i> Đặt phòng ngay
                            </a>
                        </div>
                    </div>

                    {{-- Khuyến mại liên quan --}}
                    @if($relatedPromotions->count() > 0)
                        <div class="sidebar-box">
                            <h5 class="mb-3">Khuyến mại khác</h5>
                            @foreach($relatedPromotions as $related)
                                <div class="promotion-item mb-3 p-3 border rounded">
                                    <h6 class="mb-2">
                                        <a href="{{ route('promotions.show', $related->id) }}" class="text-dark">
                                            {{ $related->title }}
                                        </a>
                                    </h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge badge-success">{{ $related->discount_text }}</span>
                                        <small class="text-muted">{{ $related->expired_at->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            @endforeach
                            <div class="text-center">
                                <a href="{{ route('promotions.index') }}" class="btn btn-outline-primary btn-sm">
                                    Xem tất cả khuyến mại
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Call to action --}}
                    <div class="sidebar-box bg-light p-4 rounded text-center">
                        <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                        <h5>Cần hỗ trợ?</h5>
                        <p>Liên hệ với chúng tôi để được tư vấn về khuyến mại</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary">
                            <i class="fas fa-phone"></i> Liên hệ ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.detail-item {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.detail-item:last-child {
    border-bottom: none;
}

.badge-lg {
    font-size: 1.1em;
    padding: 10px 15px;
}

.status-badge .badge {
    font-size: 1.2em;
}

.promotion-item {
    transition: all 0.3s ease;
}

.promotion-item:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.sidebar-box {
    margin-bottom: 2rem;
}

.promotion-card {
    transition: transform 0.3s ease;
}

.promotion-card:hover {
    transform: translateY(-3px);
}
</style>

<script>
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check text-success"></i>';
            button.classList.add('btn-success');
            button.classList.remove('btn-outline-secondary');
            
            setTimeout(function() {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Click handler cho promotion items
    const promotionItems = document.querySelectorAll('.promotion-item');
    promotionItems.forEach(item => {
        item.addEventListener('click', function() {
            const link = this.querySelector('a');
            if (link) {
                window.location.href = link.href;
            }
        });
    });
});
</script>
@endsection 