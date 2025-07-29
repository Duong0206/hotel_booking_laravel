@extends('admin.layouts.admin-master')

@section('content')
<div class="container mt-4">
    <h2>Chỉnh sửa danh mục dịch vụ</h2>
    <form action="{{ route('admin.service-categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Tên danh mục</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $category->name) }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('admin.service-categories.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection 