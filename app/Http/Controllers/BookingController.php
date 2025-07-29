<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\Services\BookingServiceInterface;


use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(
        BookingServiceInterface $bookingService
    ) {
        $this->bookingService = $bookingService;
    }

    // ==================== BOOKING METHODS ====================

    /**
     * Hiển thị trang đặt phòng
     */
    public function booking()
    {
        try {
            $data = $this->bookingService->getBookingPageData();
            return view('client.booking.index', $data);
        } catch (\Exception $e) {
            return redirect()->route('verification.notice')
                ->with('warning', $e->getMessage());
        }
    }

    /**
     * Lưu thông tin đặt phòng
     */
    public function storeBooking(Request $request)
    {
        try {
            $booking = $this->bookingService->createBooking($request->all());
            return redirect()->route('confirm-info-payment', ['booking' => $booking->id]);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Hiển thị danh sách đặt phòng của người dùng
     */
    public function myBookings()
    {
        $bookings = $this->bookingService->getCurrentUserBookings();
        return view('client.profile.bookings.index', compact('bookings'));
    }

    /**
     * Hủy đặt phòng
     */
    public function cancelBooking($id)
    {
        try {
            $this->bookingService->cancelBooking($id);
            return back()->with('success', 'Đã hủy đặt phòng thành công.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    /**
     * Hiển thị chi tiết booking cho người dùng
     */
    public function showDetail($id)
    {
        $booking = $this->bookingService->getBookingDetail($id);
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đặt phòng này.');
        }
        return view('client.booking.detail', compact('booking'));
    }

    /**
     * Trả về partial danh sách đặt phòng cho AJAX
     */
    public function partial()
    {
        $bookings = $this->bookingService->getCurrentUserBookings();
        return view('client.profile.bookings.partial', compact('bookings'));
    }

    /**
     * Trả về partial chi tiết booking cho AJAX/modal (client)
     */
    public function detailPartial($id)
    {
        $booking = $this->bookingService->getBookingDetail($id);
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đặt phòng này.');
        }
        return view('client.profile.bookings.detail', compact('booking'));
    }

    // ==================== BOOKING NOTE METHODS ====================

    /**
     * Hiển thị danh sách ghi chú của một booking
     */
    public function notesIndex(Request $request, $bookingId)
    {
        try {
            $notes = $this->bookingService->getVisibleNotes($bookingId);
            
            $notesWithPermissions = $notes->map(function ($note) {
                $note->can_edit = $this->bookingService->canEditNote($note->id);
                $note->can_delete = $this->bookingService->canDeleteNote($note->id);
                $note->type_text = $this->getTypeText($note->type);
                $note->visibility_text = $this->getVisibilityText($note->visibility);
                return $note;
            });
            
            return view('booking-notes.index', compact('notesWithPermissions', 'bookingId'));
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể tải danh sách ghi chú: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form tạo ghi chú
     */
    public function notesCreate($bookingId)
    {
        $booking = \App\Models\Booking::findOrFail($bookingId);
        return view('booking-notes.create', compact('bookingId', 'booking'));
    }

    /**
     * Lưu ghi chú mới
     */
    public function notesStore(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'content' => 'required|string|max:1000',
                'type' => 'required|in:customer,staff,admin',
                'visibility' => 'required|in:public,private,internal',
                'is_internal' => 'boolean'
            ]);

            $note = $this->bookingService->createNote($validatedData);
            
            $user = Auth::user();
            if ($user->role && $user->role->name === 'admin') {
                return redirect()->route('admin.bookings.show', $request->booking_id)
                    ->with('success', 'Tạo ghi chú thành công');
            } else {
                return redirect()->route('booking-notes.index', $request->booking_id)
                    ->with('success', 'Tạo ghi chú thành công');
            }
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Không thể tạo ghi chú: ' . $e->getMessage());
        }
    }

    /**
     * Tạo yêu cầu từ customer
     */
    public function notesStoreRequest(Request $request, $bookingId): RedirectResponse
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            $note = $this->bookingService->createCustomerRequest(
                $bookingId,
                $request->content,
                Auth::id()
            );

            return back()->with('success', 'Yêu cầu của bạn đã được gửi thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể gửi yêu cầu: ' . $e->getMessage());
        }
    }

    /**
     * Tạo phản hồi từ admin/staff
     */
    public function notesStoreResponse(Request $request, $bookingId): RedirectResponse
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000',
                'visibility' => 'required|in:public,internal',
            ]);

            $note = $this->bookingService->createAdminResponse(
                $bookingId,
                $request->content,
                Auth::user()->role->name
            );

            return back()->with('success', 'Phản hồi đã được gửi thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể gửi phản hồi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form chỉnh sửa ghi chú
     */
    public function notesEdit($bookingId, $id)
    {
        try {
            $note = $this->bookingService->getNoteById($id);
            
            if (!$note) {
                return back()->with('error', 'Không tìm thấy ghi chú');
            }
            
            if (!$this->bookingService->canEditNote($id)) {
                return back()->with('error', 'Bạn không có quyền chỉnh sửa ghi chú này');
            }
            
            $booking = $note->booking;
            return view('booking-notes.edit', compact('note', 'booking'));
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể tải ghi chú: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật ghi chú
     */
    public function notesUpdate(Request $request, $bookingId, $id)
    {
        try {
            $validatedData = $request->validate([
                'content' => 'required|string|max:1000',
                'visibility' => 'sometimes|in:public,private,internal'
            ]);

            $success = $this->bookingService->updateNote($id, $validatedData);
            
            if ($success) {
                $user = Auth::user();
                $note = $this->bookingService->getNoteById($id);
                if ($user->role && $user->role->name === 'admin') {
                    return redirect()->route('admin.bookings.show', $note->booking_id)
                        ->with('success', 'Cập nhật ghi chú thành công');
                } else {
                    return back()->with('success', 'Cập nhật ghi chú thành công');
                }
            } else {
                return back()->with('error', 'Không thể cập nhật ghi chú');
            }
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Không thể cập nhật ghi chú: ' . $e->getMessage());
        }
    }

    /**
     * Xóa ghi chú
     */
    public function notesDestroy($bookingId, $id)
    {
        try {
            $note = $this->bookingService->getNoteById($id);
            
            if (!$note) {
                return back()->with('error', 'Không tìm thấy ghi chú');
            }
            
            if (!$this->bookingService->canDeleteNote($id)) {
                return back()->with('error', 'Bạn không có quyền xóa ghi chú này');
            }
            
            $bookingId = $note->booking_id;
            $success = $this->bookingService->deleteNote($id);
            
            if ($success) {
                $user = Auth::user();
                if ($user->role && $user->role->name === 'admin') {
                    return redirect()->route('admin.bookings.show', $bookingId)
                        ->with('success', 'Xóa ghi chú thành công');
                } else {
                    return back()->with('success', 'Xóa ghi chú thành công');
                }
            } else {
                return back()->with('error', 'Không thể xóa ghi chú');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xóa ghi chú: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết ghi chú
     */
    public function notesShow($bookingId, $id)
    {
        try {
            $note = $this->bookingService->getNoteById($id);
            
            if (!$note) {
                return back()->with('error', 'Không tìm thấy ghi chú');
            }
            
            if (!$this->bookingService->canViewNote($id)) {
                return back()->with('error', 'Bạn không có quyền xem ghi chú này');
            }
            
            return view('booking-notes.show', compact('note'));
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể tải ghi chú: ' . $e->getMessage());
        }
    }

    /**
     * Tìm kiếm ghi chú
     */
    public function notesSearch(Request $request, $bookingId)
    {
        try {
            $request->validate([
                'keyword' => 'required|string|min:1|max:100'
            ]);

            $keyword = $request->input('keyword');
            $notes = $this->bookingService->searchNotes($bookingId, $keyword);
            
            $notesWithPermissions = $notes->map(function ($note) {
                $note->can_edit = $this->bookingService->canEditNote($note->id);
                $note->can_delete = $this->bookingService->canDeleteNote($note->id);
                $note->type_text = $this->getTypeText($note->type);
                $note->visibility_text = $this->getVisibilityText($note->visibility);
                return $note;
            });
            
            return view('booking-notes.index', compact('notesWithPermissions', 'bookingId', 'keyword'));
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể tìm kiếm ghi chú: ' . $e->getMessage());
        }
    }

    /**
     * Trả về partial danh sách ghi chú cho AJAX
     */
    public function notesPartial()
    {
        // Lấy tất cả booking của user hiện tại
        $bookings = $this->bookingService->getCurrentUserBookings();
        $allNotes = collect();
        
        // Lấy ghi chú từ tất cả booking
        foreach ($bookings as $booking) {
            $notes = $this->bookingService->getVisibleNotes($booking->id);
            $allNotes = $allNotes->merge($notes);
        }
        
        return view('client.profile.notes.partial', compact('allNotes'));
    }

    // ==================== HELPER METHODS ====================

    /**
     * Lấy text cho loại ghi chú
     */
    private function getTypeText(string $type): string
    {
        $types = [
            'customer' => 'Khách hàng',
            'staff' => 'Nhân viên',
            'admin' => 'Quản trị'
        ];
        
        return $types[$type] ?? $type;
    }

    /**
     * Lấy text cho quyền xem
     */
    private function getVisibilityText(string $visibility): string
    {
        $visibilities = [
            'public' => 'Công khai',
            'private' => 'Riêng tư',
            'internal' => 'Nội bộ'
        ];
        
        return $visibilities[$visibility] ?? $visibility;
    }
}
