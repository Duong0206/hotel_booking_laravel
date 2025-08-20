# Hệ Thống Kiểm Tra Trạng Thái Online/Offline

## Tổng Quan

Hệ thống này cho phép admin theo dõi trạng thái hoạt động của khách hàng trong thời gian thực với **Realtime Activity Monitoring**. Khi khách hàng thoát trình duyệt hoặc không hoạt động, trạng thái sẽ tự động chuyển sang offline.

## ✨ Tính Năng Mới: Realtime Activity Monitoring

### 🎯 **Theo Dõi Hoạt Động Chi Tiết**
- **Mouse Events**: Di chuyển chuột, click, mousedown
- **Keyboard Events**: Nhấn phím, keydown, keypress
- **Scroll Events**: Cuộn trang với debouncing
- **Touch Events**: Chạm màn hình (mobile)
- **Window Events**: Focus, blur, resize, visibility change
- **Page Lifecycle**: Load, unload, pagehide, pageshow
- **Network Status**: Online/offline detection

### 💬 **Chat-Specific Events (Mới!)**
- **Typing Detection**: Phát hiện khi user đang gõ tin nhắn
- **Input Focus/Blur**: Theo dõi khi user focus vào input
- **Message Sending**: Phát hiện khi user gửi tin nhắn
- **Content Operations**: Copy, paste nội dung
- **Real-time Typing Status**: Hiển thị "Đang gõ..." cho admin

### 📊 **Thống Kê Hoạt Động Realtime**
- **Total Events**: Tổng số sự kiện đã ghi nhận
- **Last Activity**: Thời gian hoạt động cuối cùng
- **Activity Level**: Mức độ hoạt động (0-100%)
- **Session Duration**: Thời gian session hiện tại
- **Typing Status**: Trạng thái đang gõ tin nhắn
- **Real-time Updates**: Cập nhật mỗi 10 giây

## Cách Hoạt Động

### 1. Phía Client (Khách hàng)

- **Realtime Event Monitoring**: Lắng nghe tất cả hoạt động của user
- **Activity Tracking**: Ghi nhận chi tiết từng sự kiện với timestamp
- **Smart Heartbeat**: Gửi tín hiệu dựa trên hoạt động thực tế
- **Activity Analysis**: Phân tích mức độ hoạt động và thời gian không hoạt động

### 2. Phía Server (Admin)

- **Enhanced Status**: Trạng thái online/offline với thông tin hoạt động
- **Activity Statistics**: Lưu trữ và truy xuất thống kê hoạt động
- **Real-time Updates**: Cập nhật trạng thái ngay lập tức
- **Smart Caching**: Cache thông minh với thời gian hết hạn

## 🚀 Các Route Mới

### Admin Routes
```
POST /admin/support/conversation/{id}/status - Cập nhật trạng thái user
GET /admin/support/conversation/{id}/user-status - Lấy trạng thái user + activity stats
```

### Client Routes
```
POST /support/conversation/{id}/status - Cập nhật trạng thái của chính mình
POST /support/conversation/{id}/activity-stats - Cập nhật thống kê hoạt động
POST /support/conversation/{id}/typing-status - Cập nhật trạng thái typing (MỚI!)
```

## 📈 Cấu Trúc Dữ Liệu Mới

### User Status Cache
```php
$key = "user_online_status_{$userId}";
$data = [
    'status' => 'online|offline',
    'last_seen' => timestamp,
    'conversation_id' => $conversationId,
    'is_typing' => true|false, // Trạng thái typing (MỚI!)
    'typing_started' => timestamp|null, // Thời gian bắt đầu gõ (MỚI!)
    'activity_stats' => [
        'total_events' => 150,
        'last_activity' => timestamp,
        'is_active' => true,
        'page_visible' => true,
        'network_status' => 'online',
        'session_duration' => 300000,
        'is_typing' => true, // Trạng thái typing trong activity stats
        'typing_started' => timestamp // Thời gian bắt đầu gõ
    ],
    'last_updated' => timestamp
];
```

### Typing Status Cache (MỚI!)
```php
$key = "user_typing_status_{$userId}_{$conversationId}";
$data = [
    'user_id' => $userId,
    'conversation_id' => $conversationId,
    'is_typing' => true,
    'started_at' => timestamp,
    'updated_at' => timestamp
];
```

### Activity Stats Cache
```php
$key = "user_activity_stats_{$userId}_{$conversationId}";
$data = [
    'user_id' => $userId,
    'conversation_id' => $conversationId,
    'stats' => $activityStats,
    'updated_at' => now()
];
```

## ⚡ Thời Gian Hết Hạn

- **Online Status**: 5 phút (300 giây)
- **Offline Status**: 1 phút (60 giây)
- **Activity Stats**: 10 phút (600 giây)
- **Activity Check**: Mỗi 10 giây
- **Heartbeat**: Mỗi 30 giây
- **Stats Update**: Mỗi phút

## 🎮 Các Sự Kiện Được Theo Dõi

### Mouse & Touch Events
1. **mousemove** - Di chuyển chuột (với tọa độ)
2. **click** - Nhấp chuột
3. **mousedown** - Nhấn chuột xuống
4. **touchstart** - Chạm màn hình

### Keyboard Events
5. **keypress** - Nhấn phím
6. **keydown** - Nhấn phím xuống

### Scroll & Window Events
7. **scroll** - Cuộn trang (debounced)
8. **resize** - Thay đổi kích thước cửa sổ
9. **focus/blur** - Focus/blur cửa sổ

### Page Lifecycle Events
10. **visibilitychange** - Thay đổi hiển thị trang
11. **beforeunload** - Trước khi rời trang
12. **pagehide** - Ẩn trang
13. **pageshow** - Hiển thị trang

### Network Events
14. **online/offline** - Trạng thái kết nối mạng

## 🔧 Cách Sử Dụng Nâng Cao

### 1. Trong Admin View

```javascript
// Tự động kiểm tra trạng thái mỗi 30 giây
startUserStatusMonitoring();

// Kiểm tra trạng thái ngay lập tức
checkOnlineStatus();

// Hiển thị thông tin hoạt động chi tiết
// - Trạng thái hoạt động (Đang hoạt động/Không hoạt động)
// - Thời gian hoạt động cuối (Vừa hoạt động, X phút trước)
// - Tổng số sự kiện và thời gian session
```

### 2. Trong Client View

```javascript
// Khởi tạo OnlineStatusManager với realtime monitoring
const statusManager = new OnlineStatusManager(conversationId, userId);

// Lấy thống kê hoạt động
const stats = statusManager.getActivityStats();
console.log('Activity Level:', stats.isActive ? 'Active' : 'Inactive');
console.log('Total Events:', stats.totalEvents);
console.log('Time Since Last Activity:', stats.timeSinceLastActivity);

// Bật/tắt debug mode
statusManager.setDebugMode(true);

// Hủy khi không cần thiết
statusManager.destroy();
```

## 📱 Demo và Testing

### File Demo
```
public/client/js/online-status-demo.html
```

### Tính Năng Demo
- **Real-time Monitoring**: Bật/tắt theo dõi hoạt động
- **Activity Simulation**: Mô phỏng các hoạt động khác nhau
- **Live Statistics**: Thống kê hoạt động realtime
- **Debug Mode**: Xem logs chi tiết
- **Activity Log**: Log tất cả hoạt động

## 🛡️ Bảo Mật và Performance

### Bảo Mật
- **CSRF Protection**: Tất cả requests đều có CSRF token
- **Authentication**: Chỉ user đã đăng nhập mới có thể cập nhật
- **Authorization**: User chỉ có thể cập nhật trạng thái của chính mình
- **Rate Limiting**: Giới hạn tần suất gửi activity stats

### Performance
- **Event Debouncing**: Tránh gửi quá nhiều events
- **Smart Caching**: Cache thông minh với thời gian hết hạn
- **Batch Updates**: Gửi thống kê theo batch thay vì realtime
- **Memory Management**: Giữ chỉ 100 events gần nhất

## 🔍 Monitoring và Debug

### Console Logs
```javascript
// Bật debug mode
localStorage.setItem('debug_online_status', 'true');

// Xem logs chi tiết
console.log('OnlineStatusManager Debug:', statusManager);
console.log('Activity Stats:', statusManager.getActivityStats());
```

### Server Logs
- **Activity Updates**: Log khi cập nhật thống kê hoạt động
- **Status Changes**: Log khi thay đổi trạng thái
- **Error Handling**: Log lỗi và exceptions

## 🚨 Troubleshooting

### Vấn Đề Thường Gặp

1. **Trạng thái không cập nhật**
   - Kiểm tra console logs
   - Kiểm tra network requests
   - Kiểm tra cache configuration

2. **Activity monitoring không hoạt động**
   - Kiểm tra JavaScript errors
   - Kiểm tra event listeners
   - Kiểm tra browser compatibility

3. **Performance issues**
   - Giảm tần suất gửi activity stats
   - Tăng thời gian debouncing
   - Giảm số lượng events được track

### Debug Commands

```javascript
// Kiểm tra trạng thái hiện tại
statusManager.getActivityStats();

// Bật debug mode
statusManager.setDebugMode(true);

// Kiểm tra event listeners
console.log('Event listeners:', statusManager.activityEvents);
```

## 🔮 Tương Lai

### Short-term
- **WebSocket Integration**: Sử dụng WebSocket để realtime hơn
- **Push Notifications**: Thông báo khi user online/offline
- **Activity Analytics**: Phân tích mức độ hoạt động

### Long-term
- **Machine Learning**: Dự đoán trạng thái dựa trên pattern
- **Cross-device Sync**: Đồng bộ trạng thái giữa các thiết bị
- **Advanced Analytics**: Dashboard phân tích hoạt động chi tiết
- **Mobile App Support**: Tích hợp với mobile apps
