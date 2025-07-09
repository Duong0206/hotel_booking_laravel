@extends('client.layouts.master')

@section('title', 'Khuyến Mại')

@section('content')
<div class="hero-wrap" style="background-image: url('{{ asset('client/images/bg_1.jpg') }}');">
    <div class="overlay"></div>
    <div class="container">
        <div class="row no-gutters slider-text d-flex align-items-end justify-content-center">
            <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
                <div class="text">
                    <p class="breadcrumbs mb-2">
                        <span class="mr-2"><a href="{{ route('index') }}">Trang chủ</a></span> 
                        <span>Khuyến mại</span>
                    </p>
                    <h1 class="mb-4 bread">Khuyến Mại Đặc Biệt</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="ftco-section bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="row justify-content-center mb-5 pb-3">
                    <div class="col-md-7 text-center heading-section">
                        <h2 class="mb-4">Ưu Đãi Hấp Dẫn</h2>
                        <p>Khám phá những khuyến mại đặc biệt dành riêng cho bạn. Tiết kiệm chi phí cho kỳ nghỉ hoàn hảo!</p>
                    </div>
                </div>

                {{-- Bộ lọc --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('promotions.index') }}" class="filter-form" id="filterForm">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label for="discount_type" class="form-label small text-muted">Loại giảm giá</label>
                                    <select name="discount_type" id="discount_type" class="form-control">
                                        <option value="">Tất cả loại giảm giá</option>
                                        <option value="percentage" {{ request('discount_type') == 'percentage' ? 'selected' : '' }}>Giảm theo phần trăm</option>
                                        <option value="fixed" {{ request('discount_type') == 'fixed' ? 'selected' : '' }}>Giảm cố định</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label for="search" class="form-label small text-muted">Tìm kiếm</label>
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Tìm kiếm theo tên, mô tả hoặc mã..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4 pt-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary ml-2">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                    @if(request()->anyFilled(['discount_type', 'search']))
                                        <span class="badge badge-info ml-2">
                                            {{ $total_count }} kết quả
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Danh sách khuyến mại --}}
                @if($promotions->count() > 0)
                    <div class="row">
                        @foreach($promotions as $promotion)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    @if($promotion->image)
                                        <img src="{{ asset('storage/' . $promotion->image) }}" class="card-img-top" alt="{{ $promotion->title }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-primary d-flex align-items-center justify-content-center text-white" style="height: 200px;">
                                            <div class="text-center">
                                                <i class="fas fa-percentage fa-3x mb-2"></i>
                                                <h3>{{ $promotion->discount_text }}</h3>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title">{{ $promotion->title }}</h5>
                                            <span class="badge badge-success">{{ $promotion->discount_text }}</span>
                                        </div>
                                        
                                        <p class="card-text text-muted flex-grow-1">
                                            {{ Str::limit($promotion->description, 100) }}
                                        </p>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-calendar-alt"></i> 
                                                Hết hạn: {{ $promotion->expired_at->format('d/m/Y') }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-tag"></i> 
                                                Mã: <strong>{{ $promotion->code }}</strong>
                                            </small>
                                            @if($promotion->minimum_amount > 0)
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-money-bill-wave"></i> 
                                                    Tối thiểu: {{ number_format($promotion->minimum_amount, 0, ',', '.') }}đ
                                                </small>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('promotions.show', $promotion->id) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> Chi tiết
                                            </a>
                                            <a href="{{ route('booking') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-calendar-check"></i> Đặt phòng
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Phân trang --}}
                    <div class="row">
                        <div class="col-md-12 text-center">
                            {{ $promotions->links() }}
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <h4>Không có khuyến mại nào</h4>
                                <p>Hiện tại chưa có khuyến mại nào phù hợp với tìm kiếm của bạn.</p>
                                <a href="{{ route('promotions.index') }}" class="btn btn-primary">Xem tất cả khuyến mại</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<style>
.promotion-card .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
    border: none;
    overflow: hidden;
}

.promotion-card .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.filter-form {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    display: block;
}

.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    border-color: #6c757d;
}

.badge-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    font-size: 0.9em;
    padding: 6px 12px;
    border-radius: 12px;
}

.badge-info {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
    padding: 8px 16px;
    border-radius: 15px;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Heading Section Styles */
.heading-section h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.heading-section p {
    font-size: 1.1rem;
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 0;
}

.card-img-top {
    transition: transform 0.4s ease;
}

.promotion-card:hover .card-img-top {
    transform: scale(1.05);
}

.card-body {
    padding: 1.5rem;
}

.card-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1.3;
}

.card-text {
    line-height: 1.6;
    margin-bottom: 1.2rem;
}

.promotion-info {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 1.5rem;
}

.promotion-info small {
    margin-bottom: 8px;
    padding: 4px 0;
    display: flex;
    align-items: center;
}

.promotion-info i {
    width: 18px;
    text-align: center;
    margin-right: 8px;
    color: #667eea;
}

.btn-sm {
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-primary {
    border: 2px solid #667eea;
    color: #667eea;
}

.btn-outline-primary:hover {
    background: #667eea;
    border-color: #667eea;
    color: white;
    transform: translateY(-1px);
}

.alert-info {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border: 1px solid #2196f3;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
}

.alert-info i {
    color: #1976d2;
    margin-bottom: 1rem;
}

.alert-info h4 {
    color: #1565c0;
    margin-bottom: 1rem;
    font-weight: 600;
}

.alert-info p {
    color: #1976d2;
    margin-bottom: 1.5rem;
}

/* Pagination Styling */
.pagination {
    justify-content: center;
}

.page-link {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    color: #667eea;
    margin: 0 3px;
    padding: 10px 16px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.page-link:hover {
    background: #667eea;
    border-color: #667eea;
    color: white;
    transform: translateY(-1px);
}

.page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
}

/* Header Section */
.heading-section h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    position: relative;
    margin-bottom: 1rem;
}

.heading-section p {
    font-size: 1.1rem;
    color: #6c757d;
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .filter-form {
        padding: 20px 15px;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .promotion-info {
        padding: 12px;
    }
    
    .btn-sm {
        font-size: 0.85rem;
        padding: 6px 12px;
    }
    
    .heading-section h2 {
        font-size: 2rem;
    }
}

@media (max-width: 576px) {
    .filter-form {
        padding: 15px 10px;
    }
    
    .heading-section h2 {
        font-size: 1.8rem;
    }
    
    .card-title {
        font-size: 1.1rem;
    }
    
    .promotion-info small {
        font-size: 0.8rem;
    }
}

/* Animation cho cards */
.ftco-animate {
    opacity: 0;
    animation: fadeInUp 0.6s ease forwards;
}

.ftco-animate:nth-child(1) { animation-delay: 0.1s; }
.ftco-animate:nth-child(2) { animation-delay: 0.2s; }
.ftco-animate:nth-child(3) { animation-delay: 0.3s; }
.ftco-animate:nth-child(4) { animation-delay: 0.4s; }
.ftco-animate:nth-child(5) { animation-delay: 0.5s; }
.ftco-animate:nth-child(6) { animation-delay: 0.6s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Toast container */
#toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

/* Loading state */
.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto submit form khi thay đổi discount_type
    const discountTypeSelect = document.getElementById('discount_type');
    if (discountTypeSelect) {
        discountTypeSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
    
    // Enter to submit search
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('filterForm').submit();
            }
        });
    }
    
    // Thêm loading state cho form
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalHTML = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tìm...';
                submitBtn.disabled = true;
                
                // Restore nếu có lỗi
                setTimeout(() => {
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.disabled = false;
                }, 10000);
            }
        });
    }
    
    // Animation cho promotion cards
    const promotionCards = document.querySelectorAll('.promotion-card');
    promotionCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Copy promotion code functionality
    window.copyPromotionCode = function(code) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(code).then(function() {
                showToast('Đã sao chép mã: ' + code, 'success');
            }).catch(function(err) {
                console.error('Không thể sao chép: ', err);
                fallbackCopy(code);
            });
        } else {
            fallbackCopy(code);
        }
    };
    
    // Fallback copy function
    function fallbackCopy(code) {
        const textArea = document.createElement('textarea');
        textArea.value = code;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showToast('Đã sao chép mã: ' + code, 'success');
        } catch (err) {
            console.error('Fallback copy cũng thất bại: ', err);
            showToast('Không thể sao chép. Vui lòng copy thủ công: ' + code, 'warning');
        }
        
        document.body.removeChild(textArea);
    }
});

// Toast notification function
function showToast(message, type = 'info') {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        `;
        document.body.appendChild(toastContainer);
    }
    
    const alertClass = type === 'success' ? 'alert-success' : 
                     type === 'warning' ? 'alert-warning' : 
                     type === 'error' ? 'alert-danger' : 'alert-info';
    
    const toast = document.createElement('div');
    toast.className = `alert ${alertClass} alert-dismissible fade show`;
    toast.style.cssText = `
        margin-bottom: 10px;
        min-width: 300px;
        max-width: 400px;
        animation: slideInToast 0.3s ease;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    `;
    
    const icon = type === 'success' ? 'fa-check-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' :
                type === 'error' ? 'fa-times-circle' : 'fa-info-circle';
    
    toast.innerHTML = `
        <i class="fas ${icon} mr-2"></i>${message}
        <button type="button" class="close" onclick="this.parentElement.remove()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 300);
        }
    }, 4000);
}

// Animation styles
const animationStyle = document.createElement('style');
animationStyle.textContent = `
    @keyframes slideInToast {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .alert {
        position: relative;
        border: none;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
        border-left: 4px solid #28a745;
    }
    
    .alert-warning {
        background: linear-gradient(135deg, #fff3cd, #ffeaa7);
        color: #856404;
        border-left: 4px solid #ffc107;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #f8d7da, #f5c6cb);
        color: #721c24;
        border-left: 4px solid #dc3545;
    }
    
    .alert-info {
        background: linear-gradient(135deg, #d1ecf1, #bee5eb);
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }
`;
document.head.appendChild(animationStyle);
</script>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-card {
    animation: fadeInUp 0.6s ease forwards;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.form-label {
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
}

.filter-form {
    position: relative;
}

.filter-form::before {
    content: '';
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    background: linear-gradient(45deg, transparent, rgba(0,123,255,0.1), transparent);
    border-radius: 10px;
    z-index: -1;
}
</style>
@endsection 