{{-- Component hiển thị khuyến mại cho loại phòng --}}
<div class="promotions-section">
    @if($roomType->promotions && $roomType->promotions->count() > 0)
        @foreach($roomType->promotions as $promotion)
            <div class="promotion-item">
                <div class="promotion-header">
                    <span class="promotion-label">Khuyến mại:</span>
                    <span class="promotion-value">{{ $promotion->discount_text }}</span>
                </div>
                <div class="promotion-title">{{ $promotion->title }}</div>
            </div>
        @endforeach
    @else
        <div class="promotion-item">
            <div class="promotion-header">
                <span class="promotion-label">Khuyến mại:</span>
            </div>
            <div class="promotion-text">Chưa có khuyến mãi phù hợp</div>
        </div>
    @endif
</div> 