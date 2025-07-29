@extends('admin.layouts.admin-master')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Chi tiết yêu cầu hỗ trợ #{{ $ticket->id }}</h2>
    <div class="mb-3"><b>Khách hàng:</b> {{ $ticket->user->name ?? 'N/A' }} | <b>Chủ đề:</b> {{ $ticket->subject }}</div>
    <div class="card mb-4">
        <div class="card-header">Lịch sử trao đổi</div>
        <div class="card-body" style="max-height:400px; overflow-y:auto;">
            @foreach($ticket->messages as $msg)
                <div class="mb-2 {{ $msg->sender_type == 'admin' ? 'text-end' : 'text-start' }}">
                    <span class="badge bg-{{ $msg->sender_type == 'admin' ? 'primary' : 'secondary' }}">{{ $msg->sender_type == 'admin' ? 'Admin' : 'Khách' }}</span>
                    <span class="p-2 border rounded d-inline-block">{{ $msg->message }}</span>
                    <small class="text-muted ms-2">{{ $msg->created_at->format('d/m/Y H:i') }}</small>
                </div>
            @endforeach
        </div>
    </div>
    <form method="POST" action="{{ route('admin.support.sendMessage', $ticket->id) }}">
        @csrf
        <div class="mb-3">
            <textarea name="message" class="form-control" rows="2" required placeholder="Nhập tin nhắn..."></textarea>
        </div>
        <button type="submit" class="btn btn-success">Gửi</button>
    </form>
</div>
@endsection
