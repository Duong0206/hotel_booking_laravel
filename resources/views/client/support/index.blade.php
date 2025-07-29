@extends('client.layouts.master')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Yêu cầu hỗ trợ của bạn</h2>
    <form method="POST" action="{{ route('support.createTicket') }}" class="mb-4">
        @csrf
        <div class="row g-2 align-items-end">
            <div class="col-md-8">
                <input type="text" name="subject" class="form-control" placeholder="Nhập chủ đề hỗ trợ..." required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Tạo yêu cầu mới</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Chủ đề</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
            <tr>
                <td>{{ $ticket->id }}</td>
                <td>{{ $ticket->subject }}</td>
                <td>{{ $ticket->status }}</td>
                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('support.showTicket', $ticket->id) }}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('styles')
<style>
    .support-fab {
        position: fixed;
        bottom: 32px;
        right: 32px;
        z-index: 1050;
        background: #007bff;
        color: #fff;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        font-size: 2rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .support-fab:hover {
        background: #0056b3;
    }
</style>
@endpush

<!-- Nút nổi mở popup hỗ trợ -->
<div class="support-fab" data-bs-toggle="modal" data-bs-target="#supportModal" title="Hỗ trợ khách hàng">
    <i class="fas fa-comments"></i>
</div>

<!-- Modal hỗ trợ -->
<div class="modal fade" id="supportModal" tabindex="-1" aria-labelledby="supportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="supportModalLabel">Hỗ trợ khách hàng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('support.createTicket') }}" class="mb-4">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-8">
                    <input type="text" name="subject" class="form-control" placeholder="Nhập chủ đề hỗ trợ..." required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Tạo yêu cầu mới</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Chủ đề</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->id }}</td>
                    <td>{{ $ticket->subject }}</td>
                    <td>{{ $ticket->status }}</td>
                    <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('support.showTicket', $ticket->id) }}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<!-- Đảm bảo đã có Bootstrap JS và FontAwesome -->
@endpush
@endsection
