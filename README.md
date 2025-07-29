# 🏨 Hotel Booking System - Laravel 12

Hệ thống đặt phòng khách sạn được xây dựng bằng Laravel 12 với đầy đủ tính năng quản lý booking, phòng, khách hàng và đánh giá.

## ✨ Tính năng chính

### 🛏️ Quản lý phòng
- Quản lý loại phòng và phòng
- Hình ảnh và mô tả chi tiết
- Trạng thái phòng (trống, đã đặt, bảo trì)
- Tìm kiếm và lọc phòng

### 📅 Hệ thống đặt phòng
- Đặt phòng trực tuyến
- Quản lý check-in/check-out
- Tính toán giá phòng tự động
- Gia hạn và hủy đặt phòng
- Hệ thống ghi chú booking

### 👥 Quản lý khách hàng
- Đăng ký và đăng nhập
- Hồ sơ khách hàng
- Lịch sử đặt phòng
- Đánh giá và bình luận

### ⭐ Hệ thống đánh giá
- Đánh giá sao và bình luận
- Đánh giá chi tiết (vệ sinh, tiện nghi, vị trí...)
- Quản lý đánh giá (admin)
- Hiển thị đánh giá trung bình

### 🔧 Admin Panel
- Dashboard thống kê
- Quản lý booking
- Quản lý phòng và loại phòng
- Quản lý khách hàng
- Quản lý đánh giá
- Hệ thống ghi chú

## 🚀 Cài đặt

### Yêu cầu hệ thống
- PHP 8.2+
- Laravel 12
- MySQL/PostgreSQL
- Composer
- Node.js & NPM

### Cài đặt
```bash
# Clone repository
git clone [repository-url]
cd hotel_booking_laravel

# Cài đặt dependencies
composer install
npm install

# Tạo file .env
cp .env.example .env

# Tạo key ứng dụng
php artisan key:generate

# Chạy migration
php artisan migrate

# Chạy seeder (nếu có)
php artisan db:seed

# Build assets
npm run build

# Chạy server
php artisan serve
```

## 📁 Cấu trúc dự án

```
hotel_booking_laravel/
├── app/
│   ├── Console/Commands/     # Console commands
│   ├── Http/
│   │   ├── Controllers/      # Controllers
│   │   └── Middleware/       # Middleware
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic
│   ├── Repositories/         # Data access layer
│   └── Interfaces/           # Service interfaces
├── resources/
│   ├── views/
│   │   ├── admin/           # Admin views
│   │   └── client/          # Client views
│   └── css/                 # Stylesheets
├── public/
│   ├── admin/               # Admin assets
│   └── client/              # Client assets
└── database/
    ├── migrations/          # Database migrations
    └── seeders/             # Database seeders
```

## 🔧 Cấu hình

### Database
Cấu hình database trong file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_booking
DB_USERNAME=root
DB_PASSWORD=
```

### Mail
Cấu hình email để gửi thông báo:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

## 📚 Tài liệu

Xem thêm tài liệu chi tiết trong thư mục `docs/`:
- [Hệ thống ghi chú booking](docs/BOOKING_NOTES_SYSTEM.md)
- [Hướng dẫn kiến trúc](docs/architecture_guidelines.md)
- [Hướng dẫn interface](docs/interface_contract_guide.md)

## 🤝 Đóng góp

1. Fork dự án
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit thay đổi (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📄 License

Dự án này được phát hành dưới [MIT License](LICENSE).
