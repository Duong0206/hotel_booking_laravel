@extends('admin.layouts.admin-master')

@section('content')
    <div class="container mt-4">
        <div class="card shadow rounded">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-folder-open me-2"></i> Danh mục dịch vụ
                </h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-list"></i> Quản lý dịch vụ
                    </a>
                    <a href="{{ route('admin.service-categories.create') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-plus"></i> Thêm danh mục
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">STT</th>
                                <th>Tên danh mục</th>
                                <th style="width: 100px;">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $index => $category)
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('admin.service-categories.edit', $category->id) }}'">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start">{{ $category->name }}</td>
                                    <td>
                                        <form action="{{ route('admin.service-categories.destroy', $category->id) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa?')" title="Xóa">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="event.stopPropagation();">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Không có danh mục nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
