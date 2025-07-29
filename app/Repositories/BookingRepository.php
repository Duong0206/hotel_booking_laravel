<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\BookingNote;
use App\Interfaces\Repositories\BookingRepositoryInterface;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingRepository implements BookingRepositoryInterface
{
    protected $model;
    protected $noteModel;

    public function __construct(Booking $model, BookingNote $noteModel)
    {
        $this->model = $model;
        $this->noteModel = $noteModel;
    }

    // ==================== BOOKING METHODS ====================

    /**
     * Lấy tất cả đặt phòng của người dùng
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->with('room.roomType')
            ->latest()
            ->get();
    }

    /**
     * Lấy các booking đã hoàn thành và chưa đánh giá của người dùng
     *
     * @param int $userId
     * @return Collection
     */
    public function getCompletedBookingsWithoutReview(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDoesntHave('review')
            ->with(['room.roomType'])
            ->latest()
            ->get();
    }

    /**
     * Lấy các booking đã hoàn thành của người dùng (có thể đã đánh giá hoặc chưa)
     *
     * @param int $userId
     * @return Collection
     */
    public function getCompletedBookings(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where('status', 'completed')
            ->with(['room.roomType', 'review'])
            ->latest()
            ->get();
    }

    /**
     * Kiểm tra xem booking có thể đánh giá không
     *
     * @param int $bookingId
     * @param int $userId
     * @return bool
     */
    public function canBeReviewed(int $bookingId, int $userId): bool
    {
        $booking = $this->model->where('id', $bookingId)
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDoesntHave('review')
            ->first();

        return $booking !== null;
    }

    /**
     * Tạo đặt phòng mới
     *
     * @param array $data
     * @return Booking
     */
    public function createBooking(array $data): Booking
    {
        return $this->model->create($data);
    }

    /**
     * Tìm đặt phòng theo ID
     *
     * @param int $id
     * @param int $userId
     * @return Booking|null
     */
    public function findByIdAndUserId(int $id, int $userId): ?Booking
    {
        return $this->model->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Cập nhật đặt phòng
     *
     * @param Booking $booking
     * @param array $data
     * @return bool
     */
    public function update(Booking $booking, array $data): bool
    {
        return $booking->update($data);
    }

    public function getDetailById(int $id): ?Booking
    {
        return Booking::with('room.roomType')->findOrFail($id);
    }

    /**
     * Lấy tất cả đặt phòng của người dùng (có phân trang)
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByUserIdPaginate(int $userId, $perPage = 10)
    {
        return $this->model->where('user_id', $userId)
            ->with('room.roomType')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Kiểm tra xem user đã có booking hoàn thành cho loại phòng này chưa
     *
     * @param int $userId
     * @param int $roomTypeId
     * @return bool
     */
    public function hasUserCompletedBookingForRoomType(int $userId, int $roomTypeId): bool
    {
        return $this->model->where('user_id', $userId)
            ->where('status', 'completed')
            ->whereHas('room', function($query) use ($roomTypeId) {
                $query->where('room_type_id', $roomTypeId);
            })
            ->exists();
    }

    /**
     * Hàm kiểm tra phòng còn trống theo datetime
     */
    public function isRoomAvailable($roomId, $checkInDateTime, $checkOutDateTime): bool
    {
        return !$this->model->where('room_id', $roomId)
            ->where(function($query) use ($checkInDateTime, $checkOutDateTime) {
                $query->where('check_in_date', '<', $checkOutDateTime)
                      ->where('check_out_date', '>', $checkInDateTime);
            })
            ->exists();
    }

    /**
     * Tạo đặt phòng mới (alias cho interface)
     *
     * @param array $data
     * @return Booking
     */
    public function create(array $data): Booking
    {
        return $this->createBooking($data);
    }

    // ==================== BOOKING NOTE METHODS ====================

    /**
     * Lấy tất cả ghi chú của một booking
     *
     * @param int $bookingId
     * @return Collection
     */
    public function getByBookingId(int $bookingId): Collection
    {
        return $this->noteModel->where('booking_id', $bookingId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Lấy ghi chú công khai của một booking
     *
     * @param int $bookingId
     * @return Collection
     */
    public function getPublicNotesByBookingId(int $bookingId): Collection
    {
        return $this->noteModel->where('booking_id', $bookingId)
            ->where('visibility', 'public')
            ->where('is_internal', false)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Lấy ghi chú nội bộ của một booking
     *
     * @param int $bookingId
     * @return Collection
     */
    public function getInternalNotesByBookingId(int $bookingId): Collection
    {
        return $this->noteModel->where('booking_id', $bookingId)
            ->where(function($query) {
                $query->where('visibility', 'internal')
                      ->orWhere('is_internal', true);
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Lấy ghi chú mà user có thể xem
     *
     * @param int $bookingId
     * @param int $userId
     * @param array $userRoles
     * @return Collection
     */
    public function getVisibleNotesByBookingId(int $bookingId, int $userId, array $userRoles = []): Collection
    {
        $query = $this->noteModel->where('booking_id', $bookingId);

        // Admin có thể xem tất cả
        if (in_array('admin', $userRoles)) {
            return $query->with('user')->orderBy('created_at', 'desc')->get();
        }

        // Staff có thể xem ghi chú công khai và nội bộ
        if (in_array('staff', $userRoles)) {
            return $query->where(function($q) {
                $q->where('visibility', 'public')
                  ->orWhere('visibility', 'internal')
                  ->orWhere('is_internal', true);
            })->with('user')->orderBy('created_at', 'desc')->get();
        }

        // Customer chỉ xem được ghi chú công khai
        return $query->where('visibility', 'public')
            ->where('is_internal', false)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Tạo ghi chú mới
     *
     * @param array $data
     * @return BookingNote
     */
    public function createNote(array $data): BookingNote
    {
        return $this->noteModel->create($data);
    }



    /**
     * Cập nhật ghi chú
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateNote(int $id, array $data): bool
    {
        $note = $this->noteModel->find($id);
        if ($note) {
            return $note->update($data);
        }
        return false;
    }

    /**
     * Xóa ghi chú
     *
     * @param int $id
     * @return bool
     */
    public function deleteNote(int $id): bool
    {
        $note = $this->noteModel->find($id);
        if ($note) {
            return $note->delete();
        }
        return false;
    }

    /**
     * Xóa ghi chú (alias cho interface)
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->deleteNote($id);
    }

    /**
     * Tìm ghi chú theo ID
     *
     * @param int $id
     * @return BookingNote|null
     */
    public function findById(int $id): ?BookingNote
    {
        return $this->noteModel->with('user', 'booking')->find($id);
    }

    /**
     * Kiểm tra xem user có thể xem ghi chú không
     *
     * @param int $noteId
     * @param int $userId
     * @param array $userRoles
     * @return bool
     */
    public function canUserViewNote(int $noteId, int $userId, array $userRoles = []): bool
    {
        $note = $this->noteModel->find($noteId);
        if (!$note) {
            return false;
        }

        // Admin có thể xem tất cả
        if (in_array('admin', $userRoles)) {
            return true;
        }

        // Ghi chú nội bộ chỉ admin xem được
        if ($note->is_internal) {
            return false;
        }

        // Ghi chú công khai ai cũng xem được
        if ($note->visibility === 'public') {
            return true;
        }

        // Ghi chú riêng tư chỉ người tạo xem được
        if ($note->visibility === 'private') {
            return $note->user_id === $userId;
        }

        // Ghi chú nội bộ chỉ staff và admin xem được
        if ($note->visibility === 'internal') {
            return in_array('admin', $userRoles) || in_array('staff', $userRoles);
        }

        return false;
    }

    /**
     * Kiểm tra xem user có thể chỉnh sửa ghi chú không
     *
     * @param int $noteId
     * @param int $userId
     * @param array $userRoles
     * @return bool
     */
    public function canUserEditNote(int $noteId, int $userId, array $userRoles = []): bool
    {
        $note = $this->noteModel->find($noteId);
        if (!$note) {
            return false;
        }

        // Admin có thể chỉnh sửa tất cả
        if (in_array('admin', $userRoles)) {
            return true;
        }

        // Người tạo ghi chú có thể chỉnh sửa (trừ ghi chú nội bộ)
        if ($note->user_id === $userId && !$note->is_internal) {
            return true;
        }

        return false;
    }

    /**
     * Kiểm tra xem user có thể xóa ghi chú không
     *
     * @param int $noteId
     * @param int $userId
     * @param array $userRoles
     * @return bool
     */
    public function canUserDeleteNote(int $noteId, int $userId, array $userRoles = []): bool
    {
        $note = $this->noteModel->find($noteId);
        if (!$note) {
            return false;
        }

        // Admin có thể xóa tất cả
        if (in_array('admin', $userRoles)) {
            return true;
        }

        // Người tạo ghi chú có thể xóa (trừ ghi chú nội bộ)
        if ($note->user_id === $userId && !$note->is_internal) {
            return true;
        }

        return false;
    }

    /**
     * Lấy ghi chú theo loại
     *
     * @param int $bookingId
     * @param string $type
     * @return Collection
     */
    public function getByType(int $bookingId, string $type): Collection
    {
        return $this->noteModel->where('booking_id', $bookingId)
            ->where('type', $type)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Lấy ghi chú theo visibility
     *
     * @param int $bookingId
     * @param string $visibility
     * @return Collection
     */
    public function getByVisibility(int $bookingId, string $visibility): Collection
    {
        return $this->noteModel->where('booking_id', $bookingId)
            ->where('visibility', $visibility)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Lấy ghi chú có phân trang
     *
     * @param int $bookingId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $bookingId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->noteModel->where('booking_id', $bookingId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Tìm kiếm ghi chú theo từ khóa
     *
     * @param int $bookingId
     * @param string $keyword
     * @return Collection
     */
    public function search(int $bookingId, string $keyword): Collection
    {
        return $this->noteModel->where('booking_id', $bookingId)
            ->where('content', 'like', '%' . $keyword . '%')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Lấy thống kê ghi chú của booking
     *
     * @param int $bookingId
     * @return array
     */
    public function getStatistics(int $bookingId): array
    {
        $totalNotes = $this->noteModel->where('booking_id', $bookingId)->count();
        $customerNotes = $this->noteModel->where('booking_id', $bookingId)->where('type', 'customer')->count();
        $staffNotes = $this->noteModel->where('booking_id', $bookingId)->where('type', 'staff')->count();
        $adminNotes = $this->noteModel->where('booking_id', $bookingId)->where('type', 'admin')->count();
        $internalNotes = $this->noteModel->where('booking_id', $bookingId)->where('is_internal', true)->count();
        $publicNotes = $this->noteModel->where('booking_id', $bookingId)->where('visibility', 'public')->count();
        $privateNotes = $this->noteModel->where('booking_id', $bookingId)->where('visibility', 'private')->count();

        return [
            'total' => $totalNotes,
            'by_type' => [
                'customer' => $customerNotes,
                'staff' => $staffNotes,
                'admin' => $adminNotes
            ],
            'by_visibility' => [
                'public' => $publicNotes,
                'private' => $privateNotes,
                'internal' => $internalNotes
            ]
        ];
    }
}