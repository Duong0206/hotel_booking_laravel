# Hệ thống Ghi chú Booking

## Tổng quan

Hệ thống ghi chú booking được thiết kế để quản lý và theo dõi các thông tin quan trọng liên quan đến quá trình đặt phòng. Hệ thống hỗ trợ nhiều loại ghi chú khác nhau với các quyền truy cập phân cấp rõ ràng.

## Kiến trúc

### 1. Repository Pattern
- **BookingNoteRepositoryInterface**: Định nghĩa các phương thức truy xuất dữ liệu
- **BookingNoteRepository**: Triển khai các phương thức truy xuất dữ liệu

### 2. Service Pattern
- **BookingNoteServiceInterface**: Định nghĩa các phương thức xử lý nghiệp vụ
- **BookingNoteService**: Triển khai logic nghiệp vụ
- **BookingNoteEventService**: Xử lý các sự kiện tự động

### 3. Observer Pattern
- **BookingObserver**: Tự động tạo ghi chú khi có sự kiện booking

## Các loại ghi chú

### 1. Theo vai trò người tạo
- **Customer**: Ghi chú từ khách hàng
- **Staff**: Ghi chú từ nhân viên
- **Admin**: Ghi chú từ quản lý

### 2. Theo quyền xem
- **Public**: Công khai, ai cũng xem được
- **Private**: Riêng tư, chỉ người tạo xem được
- **Internal**: Nội bộ, chỉ staff và admin xem được

### 3. Theo tính chất
- **Normal**: Ghi chú thông thường
- **Internal**: Ghi chú nội bộ (chỉ admin xem được)

## Quyền truy cập

### Admin
- Xem tất cả ghi chú
- Tạo mọi loại ghi chú
- Chỉnh sửa và xóa tất cả ghi chú

### Staff
- Xem ghi chú công khai và nội bộ
- Tạo ghi chú customer và staff
- Chỉnh sửa và xóa ghi chú của mình

### Customer
- Xem ghi chú công khai và riêng tư của mình
- Tạo ghi chú customer
- Chỉnh sửa và xóa ghi chú của mình

## Ghi chú tự động

### 1. Khi tạo booking
- Ghi chú thông báo booking mới được tạo

### 2. Khi thay đổi trạng thái
- **Confirmed**: Thông báo xác nhận và nhắc nhở check-in
- **Checked-in**: Ghi chú khách đã nhận phòng
- **Checked-out**: Ghi chú khách đã trả phòng
- **Completed**: Ghi chú booking hoàn thành
- **Cancelled**: Ghi chú booking bị hủy
- **No-show**: Ghi chú khách không đến

### 3. Khi thay đổi thông tin
- Ghi chú các thay đổi về ngày, phòng, giá

### 4. Nhắc nhở tự động
- Nhắc nhở check-in trước 1 ngày
- Nhắc nhở check-out trước 1 ngày

## Sử dụng

### 1. Trong Controller
```php
use App\Interfaces\Services\BookingNoteServiceInterface;

class BookingController extends Controller
{
    protected $bookingNoteService;

    public function __construct(BookingNoteServiceInterface $bookingNoteService)
    {
        $this->bookingNoteService = $bookingNoteService;
    }

    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        $notes = $this->bookingNoteService->getVisibleNotes($id);
        
        return view('bookings.show', compact('booking', 'notes'));
    }
}
```

### 2. Trong View
```blade
<x-booking-notes :booking="$booking" :showAddButton="true" :showSearch="true" />
```

### 3. Tạo ghi chú thủ công
```php
// Tạo ghi chú thông thường
$note = $this->bookingNoteService->createNote([
    'booking_id' => $bookingId,
    'content' => 'Nội dung ghi chú',
    'type' => 'customer',
    'visibility' => 'public'
]);

// Tạo ghi chú hệ thống
$note = $this->bookingNoteService->createSystemNote($bookingId, 'Nội dung ghi chú hệ thống');

// Tạo thông báo cho khách hàng
$note = $this->bookingNoteService->createCustomerNotification($bookingId, 'Thông báo cho khách hàng');

// Tạo ghi chú nội bộ
$note = $this->bookingNoteService->createInternalNote($bookingId, 'Ghi chú nội bộ');
```

### 4. Tìm kiếm và lọc
```php
// Tìm kiếm theo từ khóa
$notes = $this->bookingNoteService->searchNotes($bookingId, 'từ khóa');

// Lọc theo loại
$customerNotes = $this->bookingNoteService->getNotesByType($bookingId, 'customer');

// Lọc theo quyền xem
$publicNotes = $this->bookingNoteService->getNotesByVisibility($bookingId, 'public');

// Lấy ghi chú gần đây
$recentNotes = $this->bookingNoteService->getRecentNotes($bookingId, 5);

// Lấy ghi chú theo ngày
$todayNotes = $this->bookingNoteService->getNotesByDate($bookingId, '2024-01-15');

// Lấy ghi chú theo khoảng thời gian
$weekNotes = $this->bookingNoteService->getNotesByDateRange($bookingId, '2024-01-01', '2024-01-07');
```

### 5. Kiểm tra quyền
```php
// Kiểm tra quyền xem
if ($this->bookingNoteService->canViewNote($noteId)) {
    // Hiển thị ghi chú
}

// Kiểm tra quyền chỉnh sửa
if ($this->bookingNoteService->canEditNote($noteId)) {
    // Hiển thị nút chỉnh sửa
}

// Kiểm tra quyền xóa
if ($this->bookingNoteService->canDeleteNote($noteId)) {
    // Hiển thị nút xóa
}
```

## Command tự động

### 1. Tạo nhắc nhở check-in
```bash
php artisan booking:create-reminders --type=check-in
```

### 2. Tạo nhắc nhở check-out
```bash
php artisan booking:create-reminders --type=check-out
```

### 3. Thiết lập cron job
Thêm vào `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Tạo nhắc nhở check-in hàng ngày lúc 9h sáng
    $schedule->command('booking:create-reminders --type=check-in')
             ->dailyAt('09:00');
    
    // Tạo nhắc nhở check-out hàng ngày lúc 9h sáng
    $schedule->command('booking:create-reminders --type=check-out')
             ->dailyAt('09:00');
}
```

## API Endpoints

### 1. Lấy danh sách ghi chú
```
GET /booking-notes/{bookingId}
```

### 2. Tạo ghi chú mới
```
POST /booking-notes
```

### 3. Tạo ghi chú qua AJAX
```
POST /booking-notes/ajax
```

### 4. Cập nhật ghi chú
```
PUT /booking-notes/{id}
```

### 5. Xóa ghi chú
```
DELETE /booking-notes/{id}
```

## Thống kê

### 1. Lấy thống kê ghi chú
```php
$statistics = $this->bookingNoteService->getNoteStatistics($bookingId);

// Kết quả:
[
    'total' => 10,
    'by_type' => [
        'customer' => 3,
        'staff' => 4,
        'admin' => 3
    ],
    'by_visibility' => [
        'public' => 6,
        'private' => 2,
        'internal' => 2
    ]
]
```

## Bảo mật

### 1. Middleware
- `CheckBookingAccess`: Kiểm tra quyền truy cập booking
- Tự động kiểm tra quyền xem/chỉnh sửa/xóa ghi chú

### 2. Validation
- Nội dung ghi chú: tối đa 1000 ký tự
- Loại ghi chú: customer, staff, admin
- Quyền xem: public, private, internal

### 3. Authorization
- Kiểm tra vai trò người dùng
- Kiểm tra quyền sở hữu ghi chú
- Phân quyền theo loại ghi chú

## Tùy chỉnh

### 1. Thêm loại ghi chú mới
1. Cập nhật migration `booking_notes` table
2. Cập nhật model `BookingNote`
3. Cập nhật service và repository
4. Cập nhật view component

### 2. Thêm quyền xem mới
1. Cập nhật migration `booking_notes` table
2. Cập nhật model `BookingNote`
3. Cập nhật logic kiểm tra quyền trong service

### 3. Tùy chỉnh ghi chú tự động
1. Cập nhật `BookingNoteEventService`
2. Cập nhật `BookingObserver`
3. Thêm command mới nếu cần

## Troubleshooting

### 1. Ghi chú không hiển thị
- Kiểm tra quyền truy cập của user
- Kiểm tra visibility của ghi chú
- Kiểm tra relationship giữa booking và notes

### 2. Không thể tạo ghi chú
- Kiểm tra validation rules
- Kiểm tra quyền tạo ghi chú
- Kiểm tra CSRF token

### 3. Ghi chú tự động không được tạo
- Kiểm tra Observer đã được đăng ký
- Kiểm tra Event Service
- Kiểm tra relationship giữa các model

## Performance

### 1. Tối ưu truy vấn
- Sử dụng eager loading cho relationship
- Thêm index cho các trường thường query
- Sử dụng pagination cho danh sách lớn

### 2. Cache
- Cache thống kê ghi chú
- Cache danh sách ghi chú công khai
- Sử dụng Redis cho cache

### 3. Background Jobs
- Sử dụng queue cho việc tạo ghi chú tự động
- Sử dụng job cho việc gửi thông báo
- Sử dụng batch job cho việc xử lý hàng loạt