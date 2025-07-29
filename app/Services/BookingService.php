<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingNote;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\Services\BookingServiceInterface;

use App\Interfaces\Repositories\RoomRepositoryInterface;
use App\Interfaces\Repositories\BookingRepositoryInterface;

use App\Interfaces\Repositories\RoomTypeRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingService implements BookingServiceInterface
{
    protected $bookingRepository;
    protected $roomRepository;
    protected $roomTypeRepository;
    public function __construct(
        BookingRepositoryInterface $bookingRepository,
        RoomRepositoryInterface $roomRepository,
        RoomTypeRepositoryInterface $roomTypeRepository
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->roomRepository = $roomRepository;
        $this->roomTypeRepository = $roomTypeRepository;
    }

    // ==================== BOOKING METHODS ====================

    /**
     * Lấy thông tin trang đặt phòng
     *
     * @return array
     */
    public function getBookingPageData(): array
    {
        $user = Auth::user();

        // Kiểm tra người dùng đã xác thực email chưa
        if ($user->email_verified_at === null) {
            throw ValidationException::withMessages([
                'email' => ['Bạn cần xác thực email trước khi đặt phòng.'],
            ]);
        }

        // Lấy danh sách loại phòng
        $roomTypes = $this->roomTypeRepository->getAllRoomTypes();

        return [
            'user' => $user,
            'roomTypes' => $roomTypes
        ];
    }

    /**
     * Tạo đặt phòng mới
     *
     * @param array $data
     * @return Booking
     */
    public function createBooking(array $data): Booking
    {
        // 1. Validation
        $validator = Validator::make($data, [
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Ép kiểu về string
        $checkInDate = is_object($data['check_in_date']) ? $data['check_in_date']->format('Y-m-d') : $data['check_in_date'];
        $checkOutDate = is_object($data['check_out_date']) ? $data['check_out_date']->format('Y-m-d') : $data['check_out_date'];

        $checkInDateTime = $checkInDate . ' 14:00:00';
        $checkOutDateTime = $checkOutDate . ' 12:00:00';

        // 3. Kiểm tra phòng trống, tạo booking như cũ nhưng dùng $checkInDateTime, $checkOutDateTime
        $availableRoom = $this->roomRepository->findAvailableRoomByType(
            $data['room_type_id'],
            $checkInDateTime,
            $checkOutDateTime
        );

        if (!$availableRoom) {
            throw ValidationException::withMessages([
                'room' => ['Hiện không còn phòng trống cho loại phòng này trong thời gian đã chọn.'],
            ]);
        }

        // 4. Tính tiền
        $checkInDate = (new \DateTime($checkInDateTime))->format('Y-m-d');
        $checkOutDate = (new \DateTime($checkOutDateTime))->format('Y-m-d');
        $nights = (new \DateTime($checkInDate))->diff(new \DateTime($checkOutDate))->days;
        if ($nights < 1) $nights = 1;
        $totalPrice = $availableRoom->roomType->price * $nights;

        // 5. Tạo booking ID
        $bookingId = 'BK' . date('ymd') . strtoupper(Str::random(5));

        $bookingData = [
            'user_id' => Auth::id(),
            'booking_id' => $bookingId,
            'room_id' => $availableRoom->id,
            'check_in_date' => $checkInDateTime,  // Lưu vào DB dạng datetime
            'check_out_date' => $checkOutDateTime,
            'price' => $totalPrice,
            'status' => 'pending',
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];

        Log::info([
            'check_in' => $checkInDateTime,
            'check_out' => $checkOutDateTime,
            'nights' => $nights,
            'room_price' => $availableRoom->roomType->price,
            'total_price' => $totalPrice,
        ]);
        return $this->bookingRepository->create($bookingData);
    }

    /**
     * Lấy danh sách đặt phòng của người dùng hiện tại (có phân trang)
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCurrentUserBookings($perPage = 10)
    {
        return $this->bookingRepository->getByUserIdPaginate(Auth::id(), $perPage);
    }

    /**
     * Lấy các booking đã hoàn thành và chưa đánh giá của người dùng hiện tại
     *
     * @return Collection
     */
    public function getCompletedBookingsWithoutReview(): Collection
    {
        return $this->bookingRepository->getCompletedBookingsWithoutReview(Auth::id());
    }

    /**
     * Lấy các booking đã hoàn thành của người dùng hiện tại
     *
     * @return Collection
     */
    public function getCompletedBookings(): Collection
    {
        return $this->bookingRepository->getCompletedBookings(Auth::id());
    }

    /**
     * Kiểm tra xem booking có thể đánh giá không
     *
     * @param int $bookingId
     * @return bool
     */
    public function canBeReviewed(int $bookingId): bool
    {
        return $this->bookingRepository->canBeReviewed($bookingId, Auth::id());
    }

    /**
     * Hủy đặt phòng
     *
     * @param int $bookingId
     * @return bool
     */
    public function cancelBooking(int $bookingId): bool
    {
        // Tìm đặt phòng
        $booking = $this->bookingRepository->findByIdAndUserId($bookingId, Auth::id());

        // Nếu không tìm thấy hoặc không phải của người dùng hiện tại
        if (!$booking) {
            throw ValidationException::withMessages([
                'booking' => ['Không tìm thấy đặt phòng.'],
            ]);
        }

        // Kiểm tra trạng thái
        if ($booking->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => ['Không thể hủy đặt phòng này.'],
            ]);
        }

        // Cập nhật trạng thái
        return $this->bookingRepository->update($booking, ['status' => 'cancelled']);
    }

    public function getBookingDetail(int $id): Booking
    {
        return $this->bookingRepository->getDetailById($id);
    }

    /**
     * Lấy danh sách đặt phòng theo userId
     *
     * @param int $userId
     * @return Collection
     */
    public function getBookingsByUser(int $userId): Collection
    {
        return $this->bookingRepository->getByUserId($userId);
    }

    // ==================== BOOKING NOTE METHODS ====================

    /**
     * Lấy ghi chú của một booking mà user có thể xem
     */
    public function getVisibleNotes(int $bookingId): Collection
    {
        $user = Auth::user();
        $userRoles = $this->getUserRoles($user);

        return $this->bookingRepository->getVisibleNotesByBookingId(
            $bookingId,
            $user->id,
            $userRoles
        );
    }

    /**
     * Tạo ghi chú mới
     */
    public function createNote(array $data): BookingNote
    {
        $user = Auth::user();

        // Validate dữ liệu
        $validator = Validator::make($data, [
            'booking_id' => 'required|exists:bookings,id',
            'content' => 'required|string|max:1000',
            'type' => 'required|in:customer,staff,admin,system',
            'visibility' => 'required|in:public,private,internal',
            'is_internal' => 'boolean'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Kiểm tra quyền tạo ghi chú
        $this->validateNoteCreationPermission($user, $data);

        // Chuẩn bị dữ liệu
        $noteData = [
            'booking_id' => $data['booking_id'],
            'user_id' => $user->id,
            'content' => $data['content'],
            'type' => $data['type'],
            'visibility' => $data['visibility'],
            'is_internal' => $data['is_internal'] ?? false
        ];

        return $this->bookingRepository->createNote($noteData);
    }

    /**
     * Cập nhật ghi chú
     */
    public function updateNote(int $noteId, array $data): bool
    {
        $user = Auth::user();
        $userRoles = $this->getUserRoles($user);

        // Kiểm tra quyền chỉnh sửa
        if (!$this->bookingRepository->canUserEditNote($noteId, $user->id, $userRoles)) {
            throw new \Exception('Bạn không có quyền chỉnh sửa ghi chú này.');
        }

        // Validate dữ liệu
        $validator = Validator::make($data, [
            'content' => 'required|string|max:1000',
            'visibility' => 'sometimes|in:public,private,internal'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->bookingRepository->updateNote($noteId, $data);
    }

    /**
     * Xóa ghi chú
     */
    public function deleteNote(int $noteId): bool
    {
        $user = Auth::user();
        $userRoles = $this->getUserRoles($user);

        // Kiểm tra quyền xóa
        if (!$this->bookingRepository->canUserDeleteNote($noteId, $user->id, $userRoles)) {
            throw new \Exception('Bạn không có quyền xóa ghi chú này.');
        }

        return $this->bookingRepository->delete($noteId);
    }

    /**
     * Lấy ghi chú theo loại
     */
    public function getNotesByType(int $bookingId, string $type): Collection
    {
        return $this->bookingRepository->getByType($bookingId, $type);
    }

    /**
     * Lấy ghi chú theo visibility
     */
    public function getNotesByVisibility(int $bookingId, string $visibility): Collection
    {
        return $this->bookingRepository->getByVisibility($bookingId, $visibility);
    }

    /**
     * Lấy ghi chú có phân trang
     */
    public function getPaginatedNotes(int $bookingId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->bookingRepository->getPaginated($bookingId, $perPage);
    }

    /**
     * Tìm kiếm ghi chú
     */
    public function searchNotes(int $bookingId, string $keyword): Collection
    {
        return $this->bookingRepository->search($bookingId, $keyword);
    }

    /**
     * Lấy thống kê ghi chú
     */
    public function getNoteStatistics(int $bookingId): array
    {
        return $this->bookingRepository->getStatistics($bookingId);
    }

    /**
     * Kiểm tra quyền xem ghi chú
     */
    public function canViewNote(int $noteId): bool
    {
        $user = Auth::user();
        $userRoles = $this->getUserRoles($user);

        return $this->bookingRepository->canUserViewNote($noteId, $user->id, $userRoles);
    }

    /**
     * Kiểm tra quyền chỉnh sửa ghi chú
     */
    public function canEditNote(int $noteId): bool
    {
        $user = Auth::user();
        $userRoles = $this->getUserRoles($user);

        return $this->bookingRepository->canUserEditNote($noteId, $user->id, $userRoles);
    }

    /**
     * Kiểm tra quyền xóa ghi chú
     */
    public function canDeleteNote(int $noteId): bool
    {
        $user = Auth::user();
        $userRoles = $this->getUserRoles($user);

        return $this->bookingRepository->canUserDeleteNote($noteId, $user->id, $userRoles);
    }

    /**
     * Tạo ghi chú hệ thống tự động
     */
    public function createSystemNote(int $bookingId, string $content, string $type = 'system'): BookingNote
    {
        $noteData = [
            'booking_id' => $bookingId,
            'user_id' => 1, // System user
            'content' => $content,
            'type' => $type,
            'visibility' => 'internal',
            'is_internal' => true
        ];

        return $this->bookingRepository->createNote($noteData);
    }

    /**
     * Tạo ghi chú thông báo cho khách hàng
     */
    public function createCustomerNotification(int $bookingId, string $content): BookingNote
    {
        $noteData = [
            'booking_id' => $bookingId,
            'user_id' => 1, // System user
            'content' => $content,
            'type' => 'admin',
            'visibility' => 'public',
            'is_internal' => false
        ];

        return $this->bookingRepository->createNote($noteData);
    }

    /**
     * Tạo ghi chú nội bộ cho nhân viên
     */
    public function createInternalNote(int $bookingId, string $content): BookingNote
    {
        $noteData = [
            'booking_id' => $bookingId,
            'user_id' => 1, // System user
            'content' => $content,
            'type' => 'admin',
            'visibility' => 'internal',
            'is_internal' => true
        ];

        return $this->bookingRepository->createNote($noteData);
    }

    /**
     * Tạo yêu cầu từ customer (chỉ admin/staff mới gọi được)
     */
    public function createCustomerRequest(int $bookingId, string $content, int $customerId): BookingNote
    {
        $noteData = [
            'booking_id' => $bookingId,
            'user_id' => $customerId,
            'content' => $content,
            'type' => 'customer',
            'visibility' => 'public',
            'is_internal' => false
        ];

        return $this->bookingRepository->createNote($noteData);
    }

    /**
     * Tạo phản hồi từ admin/staff cho customer
     */
    public function createAdminResponse(int $bookingId, string $content, string $type = 'admin'): BookingNote
    {
        $noteData = [
            'booking_id' => $bookingId,
            'user_id' => Auth::id(),
            'content' => $content,
            'type' => $type,
            'visibility' => 'public',
            'is_internal' => false
        ];

        return $this->bookingRepository->createNote($noteData);
    }

    /**
     * Lấy ghi chú theo ID
     */
    public function getNoteById(int $noteId): ?BookingNote
    {
        return $this->bookingRepository->findById($noteId);
    }

    /**
     * Lấy ghi chú gần đây nhất
     */
    public function getRecentNotes(int $bookingId, int $limit = 5): Collection
    {
        return $this->bookingRepository->getByBookingId($bookingId)
            ->take($limit)
            ->sortByDesc('created_at');
    }

    /**
     * Lấy ghi chú theo ngày
     */
    public function getNotesByDate(int $bookingId, string $date): Collection
    {
        return $this->bookingRepository->getByBookingId($bookingId)
            ->filter(function ($note) use ($date) {
                return $note->created_at->format('Y-m-d') === $date;
            });
    }

    /**
     * Lấy ghi chú theo khoảng thời gian
     */
    public function getNotesByDateRange(int $bookingId, string $startDate, string $endDate): Collection
    {
        return $this->bookingRepository->getByBookingId($bookingId)
            ->filter(function ($note) use ($startDate, $endDate) {
                $noteDate = $note->created_at->format('Y-m-d');
                return $noteDate >= $startDate && $noteDate <= $endDate;
            });
    }

    // ==================== HELPER METHODS ====================

    /**
     * Kiểm tra quyền tạo ghi chú
     */
    private function validateNoteCreationPermission($user, array $data): void
    {
        $userRoles = $this->getUserRoles($user);
        
        // System type chỉ được tạo bởi hệ thống (user_id = 1)
        if ($data['type'] === 'system') {
            if ($user->id !== 1) {
                throw new \Exception('Chỉ hệ thống mới có thể tạo ghi chú hệ thống.');
            }
            return;
        }
        
        // Admin có thể tạo mọi loại ghi chú
        if (in_array('admin', $userRoles)) {
            return;
        }

        // Staff có thể tạo ghi chú customer và staff, không thể tạo admin
        if (in_array('staff', $userRoles)) {
            if ($data['type'] === 'admin') {
                throw new \Exception('Bạn không có quyền tạo ghi chú quản lý.');
            }
            // Staff có thể tạo ghi chú internal
            return;
        }

        // Customer chỉ có thể tạo ghi chú customer với visibility public
        if (in_array('customer', $userRoles)) {
            if ($data['type'] !== 'customer') {
                throw new \Exception('Bạn chỉ có thể tạo yêu cầu từ khách hàng.');
            }
            if ($data['visibility'] !== 'public') {
                throw new \Exception('Yêu cầu của bạn phải là công khai.');
            }
            return;
        }

        // Các trường hợp khác không được phép
        throw new \Exception('Bạn không có quyền tạo ghi chú. Vui lòng liên hệ admin để được hỗ trợ.');
    }

    /**
     * Lấy roles của user
     */
    private function getUserRoles($user): array
    {
        $roles = [];
        
        if ($user->role && $user->role->name === 'admin') {
            $roles[] = 'admin';
        }
        
        if ($user->role && $user->role->name === 'staff') {
            $roles[] = 'staff';
        }
        
        if ($user->role && $user->role->name === 'customer') {
            $roles[] = 'customer';
        }

        return $roles;
    }
}
