@extends('client.layouts.master')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Chi tiết yêu cầu hỗ trợ #{{ $ticket->id }}</h2>
    <div class="mb-3"><b>Chủ đề:</b> {{ $ticket->subject }}</div>
    <div class="card mb-4">
        <div class="card-header">Lịch sử trao đổi</div>
        <div class="card-body" style="max-height:400px; overflow-y:auto;">
            @foreach($ticket->messages as $msg)
                @if($msg->sender_type == 'user')
                    <div class="d-flex justify-content-end mb-2">
                        <div>
                            <span class="badge bg-primary">{{ $ticket->user && $msg->sender_id == $ticket->user->id ? $ticket->user->name : 'Người dùng' }}</span>
                            <span class="p-2 rounded d-inline-block text-white" style="background: #007bff; min-width: 80px; text-align: right;">{{ $msg->message }}</span>
                            <small class="text-muted ms-2">{{ $msg->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                @else
                    <div class="d-flex justify-content-start mb-2">
                        <div>
                            <span class="badge bg-secondary">Admin</span>
                            <span class="p-2 rounded d-inline-block text-dark" style="background: #f1f1f1; min-width: 80px; text-align: left;">{{ $msg->message }}</span>
                            <small class="text-muted ms-2">{{ $msg->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    <form method="POST" action="{{ route('support.sendMessage', $ticket->id) }}">
        @csrf
        <div class="mb-3">
            <textarea name="message" class="form-control" rows="2" required placeholder="Nhập tin nhắn..."></textarea>
        </div>
        <button type="submit" class="btn btn-success">Gửi</button>
    </form>
</div>
@endsection
