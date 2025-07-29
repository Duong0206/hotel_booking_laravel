@extends('admin.layouts.admin-master')

@section('content')
<div class="container mt-4">
    <div class="card shadow rounded">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-plug-circle-check me-2"></i>
                Gán dịch vụ cho loại phòng: <strong>{{ $roomType->name }}</strong>
            </h5>
            <a href="{{ route('admin.services.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Tạo dịch vụ
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.room-type-services.update', $roomType->id) }}" method="POST">
                @csrf
                @method('PUT')

                @php $hasServices = false; @endphp

                @foreach($categories as $category)
                    @php
                        $categoryServices = $services->where('service_category_id', $category->id);
                    @endphp

                    @if($categoryServices->isNotEmpty())
                        @php $hasServices = true; @endphp
                        <div class="mb-5">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-folder-open me-2"></i>
                                {{ $category->name }}
                                <span class="badge bg-secondary ms-2">{{ $categoryServices->count() }} dịch vụ</span>
                            </h6>

                            <div class="row g-3">
                                @foreach($categoryServices as $service)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border">
                                            <div class="card-body">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="service_ids[]" value="{{ $service->id }}"
                                                           id="service_{{ $service->id }}"
                                                           {{ in_array($service->id, $selectedServices) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold" for="service_{{ $service->id }}">
                                                        {{ $service->name }}
                                                    </label>
                                                </div>

                                                <div class="mb-2">
                                                    <span class="badge {{ $service->price == 0 ? 'bg-success' : 'bg-warning text-dark' }}">
                                                        {{ $service->price == 0 ? 'Miễn phí' : number_format($service->price, 0, ',', '.') . ' đ' }}
                                                    </span>
                                                </div>

                                                @if($service->description)
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        {{ Str::limit($service->description, 80) }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                @if(!$hasServices)
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <h5 class="text-muted">Chưa có dịch vụ nào</h5>
                        <p class="text-muted">Vui lòng tạo dịch vụ trước khi gán cho loại phòng.</p>
                        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tạo dịch vụ mới
                        </a>
                    </div>
                @else
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                        <a href="{{ route('admin.room-type-services.index') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
