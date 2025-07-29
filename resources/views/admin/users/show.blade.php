@extends('admin.layouts.admin-master')
@section('title', 'Chi tiết người dùng')
@section('header', 'Chi tiết người dùng')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Thông tin người dùng</h6>
        <div>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm">Sửa</a>
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận xóa user này?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
            </form>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
        </div>
    </div>
    <div class="card-body">
        <p><strong>ID:</strong> {{ $user->id }}</p>
        <p><strong>Họ tên:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Tên đăng nhập:</strong> {{ $user->username }}</p>
        <p><strong>Điện thoại:</strong> {{ $user->phone }}</p>
        <p><strong>Vai trò:</strong> {{ $user->role->name ?? 'Khách' }}</p>
        <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Ngày cập nhật:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
    </div>
</div>
@endsection 