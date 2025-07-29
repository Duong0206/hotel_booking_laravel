@props(['roomType', 'serviceCategories'])

<div class="modal fade" id="roomServicesModal" tabindex="-1" role="dialog" aria-labelledby="roomServicesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-dialog-centered" role="document" style="max-width: 1300px;">
        <div class="modal-content" id="roomServicesModal">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title font-weight-semibold" id="roomServicesModalLabel">
                    Dịch vụ của loại phòng: {{ $roomType->name }}
                </h5>
                <button type="button" class="btn border-0 bg-transparent ms-auto" data-bs-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body p-0" style="height: 520px; min-height: 320px;">
                <div class="d-flex h-100 flex-row flex-nowrap" style="height:100%;">

                    <!-- Hình ảnh -->
                    <div class="room-image-col d-flex align-items-center justify-content-center bg-white image-placeholder" style="flex-basis:70%; min-width:0; max-width:70%; border-right:1.5px solid #eee;">
                        <div class="col-md-12 ftco-animate p-0">
                            <div class="single-slider owl-carousel">
                                <div class="item">
                                    <div class="room-img" style="background-image: url('/client/images/room-1.jpg');"></div>
                                </div>
                                <div class="item">
                                    <div class="room-img" style="background-image: url('/client/images/room-2.jpg');"></div>
                                </div>
                                <div class="item">
                                    <div class="room-img" style="background-image: url('/client/images/room-3.jpg');"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dịch vụ -->
                    <div class="service-scroll" style="flex-basis:30%; max-width:30%; min-width:220px; padding:1.4rem 1.2rem 90px 1.2rem;">
                        @php 
                            $hasService = false; 
                        @endphp

                        @foreach ($serviceCategories ?? [] as $category)
                            @php
                                $services = ($roomType->services ?? collect())->where(
                                    'service_category_id',
                                    $category->id,
                                );
                            @endphp

                            @if ($services->count())
                                @php $hasService = true; @endphp
                                <div class="service-group">
                                    <div class="service-group-title">{{ $category->name }}</div>

                                    <div class="row">
                                        @foreach ($services->chunk(ceil($services->count() / 2)) as $chunk)
                                            <div class="col-6">
                                                <ul class="service-list">
                                                    @foreach ($chunk as $service)
                                                        <li class="service-item">
                                                            <span class="bullet">•</span>
                                                            <span class="service-name">{{ $service->name }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                            @endif
                            
                        @endforeach
                        
                        @if (!$hasService)
                            <div class="text-center text-muted no-services-message">Không có dịch vụ nào cho loại phòng
                                này.</div>
                        @endif
                        <div class="about-hotel-box text-center mt-4 mb-2 p-3 rounded shadow-sm" style="background: #f8f9fa;">
                            <h5 class="font-weight-bold mb-2" style="color: #b48c5a;">
                            Marron Hotel
                            </h5>
                            <p class="mb-0 text-muted" style="font-size: 1.05rem;">
                                Điểm đến lý tưởng cho kỳ nghỉ dưỡng và công tác của bạn. <br>
                                Chúng tôi cam kết mang đến trải nghiệm lưu trú tuyệt vời với hệ thống phòng nghỉ hiện đại, dịch vụ chuyên nghiệp và không gian sang trọng.<br>
                                <span class="font-italic">Sứ mệnh của chúng tôi là đem lại sự hài lòng tối đa cho mọi khách hàng.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Giá -->
                <div
                    class="room-price-sticky px-4 py-3 bg-white border-top d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Khởi điểm từ:</span><br>
                        <strong class="room-price text-danger" style="font-size: 1.2rem;">
                            {{ number_format($roomType->price ?? 0) }} VND
                        </strong> <span class="text-muted small">/ phòng/ đêm</span>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('booking') }}?room_type_id={{ $roomType->id }}" class="btn btn-primary py-3 px-5">Đặt phòng</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Layout */
    #roomServicesModal .modal-content {
        border-radius: 8px;
        border: 1.5px solid #e0e0e0;
        box-shadow: 0 8px 32px rgba(60,60,60,0.10), 0 1.5px 4px rgba(60,60,60,0.06);
        background: #fff;
    }

    #roomServicesModal .modal-header {
        border-bottom: 1px solid #eee;
        
    }

    #roomServicesModal .modal-title {
        font-size: 1.2rem;
        font-weight: 600;
    }

    #roomServicesModal .modal-body {
        padding: 0 !important;
        /* height: 520px; */
        /* min-height: 320px; */
        position: relative;
        padding-bottom: 80px;
    }

    #roomServicesModal .modal-dialog {
        max-width: 1300px;
    }
    #roomServicesModal .room-image-col {
        flex-basis: 70%;
        max-width: 70%;
        min-width: 0;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 1.5px solid #eee;
    }

    /* Scroll bên phải */
    #roomServicesModal .service-scroll {
        overflow-y: auto;
        height: 100%;
        max-height: 480px;
        flex-basis: 30%;
        max-width: 30%;
        min-width: 220px;
        padding: 1.4rem 1.2rem 90px 1.2rem;
        background: #f9fafb;
        scrollbar-width: thin;
    }

    #roomServicesModal .service-scroll::-webkit-scrollbar {
        width: 6px;
        background: #eee;
    }

    #roomServicesModal .service-scroll::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    /* Nhóm dịch vụ */
    #roomServicesModal .service-group {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px dashed #ddd;
    }

    #roomServicesModal .service-group-title {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #222;
    }

    /* Danh sách dịch vụ */
    #roomServicesModal .service-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    #roomServicesModal .service-item {
        font-size: 0.82rem;
        color: #444;
        margin-bottom: 0.2rem;
        display: flex;
        gap: 6px;
        line-height: 1.5;
    }

    #roomServicesModal .bullet {
        font-size: 1rem;
        line-height: 1;
    }
    /* Responsive */
    @media (max-width: 1400px) {
        #roomServicesModal .modal-dialog {
            max-width: 98vw;
        }
        #roomServicesModal .room-image-col {
            flex-basis: 65%;
            max-width: 65%;
        }
        #roomServicesModal .service-scroll {
            flex-basis: 35%;
            max-width: 35%;
        }
    }
    @media (max-width: 1100px) {
        #roomServicesModal .room-image-col {
            flex-basis: 60%;
            max-width: 60%;
        }
        #roomServicesModal .service-scroll {
            flex-basis: 40%;
            max-width: 40%;
        }
    }
    @media (max-width: 900px) {
        #roomServicesModal .modal-body {
            flex-direction: column !important;
            height: auto !important;
        }
        #roomServicesModal .room-image-col {
            max-width: 100%;
            flex-basis: 100%;
            border-right: none;
            border-bottom: 1px solid #eee;
        }
        #roomServicesModal .service-scroll {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0;
            border-left: none;
            border-top: 1px solid #eee;
            max-height: 220px;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        #roomServicesModal .room-price-sticky {
            position: static;
            box-shadow: none;
            border-top: none;
        }
    }
</style>

