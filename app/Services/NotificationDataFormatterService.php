<?php

namespace App\Services;

use Carbon\Carbon;

class NotificationDataFormatterService
{
    /**
     * Format notification data for display
     */
    public function formatData($data)
    {
        if (!is_array($data)) {
            return [];
        }

        $formattedData = [];
        
        foreach ($data as $key => $value) {
            $formattedData[] = [
                'key' => $key,
                'label' => $this->getLabel($key),
                'value' => $value,
                'formatted_value' => $this->formatValue($key, $value),
                'type' => $this->getValueType($key, $value),
                'icon' => $this->getIcon($key),
                'color' => $this->getColor($key, $value)
            ];
        }

        return $formattedData;
    }

    /**
     * Get human-readable label for key
     */
    private function getLabel($key)
    {
        $labels = [
            'user_id' => 'ID Khách hàng',
            'booking_id' => 'ID Đặt phòng',
            'note_id' => 'ID Ghi chú',
            'review_id' => 'ID Đánh giá',
            'ticket_id' => 'ID Ticket',
            'room_type_id' => 'ID Loại phòng',
            'new_status' => 'Trạng thái mới',
            'old_status' => 'Trạng thái cũ',
            'status' => 'Trạng thái',
            'booking_code' => 'Mã đặt phòng',
            'code' => 'Mã',
            'amount' => 'Số tiền',
            'price' => 'Giá',
            'total' => 'Tổng cộng',
            'email' => 'Email',
            'phone' => 'Số điện thoại',
            'name' => 'Tên',
            'title' => 'Tiêu đề',
            'message' => 'Nội dung',
            'description' => 'Mô tả',
            'created_at' => 'Tạo lúc',
            'updated_at' => 'Cập nhật lúc',
            'date' => 'Ngày',
            'time' => 'Thời gian',
            'url' => 'Liên kết',
            'link' => 'Đường dẫn',
            'is_read' => 'Đã đọc',
            'is_active' => 'Hoạt động',
            'priority' => 'Độ ưu tiên',
            'type' => 'Loại',
            'category' => 'Danh mục'
        ];

        return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Format value based on key and value type
     */
    private function formatValue($key, $value)
    {
        // Handle null values
        if (is_null($value)) {
            return '<span class="text-muted">Không có</span>';
        }

        // Handle numeric values
        if (is_numeric($value)) {
            if (in_array($key, ['user_id', 'booking_id', 'note_id', 'review_id', 'ticket_id', 'room_type_id'])) {
                return '<span class="badge bg-info">ID: ' . $value . '</span>';
            }
            if (str_contains($key, 'amount') || str_contains($key, 'price') || str_contains($key, 'total')) {
                return '<span class="text-success fw-bold">' . number_format($value) . ' VNĐ</span>';
            }
            return '<span class="badge bg-primary">' . $value . '</span>';
        }

        // Handle boolean values
        if (is_bool($value)) {
            return '<span class="badge bg-' . ($value ? 'success' : 'danger') . '">' . ($value ? 'Có' : 'Không') . '</span>';
        }

        // Handle status values
        if (in_array($key, ['new_status', 'old_status', 'status'])) {
            return $this->formatStatus($value);
        }

        // Handle code values
        if (in_array($key, ['booking_code', 'code'])) {
            return '<code class="text-primary">' . $value . '</code>';
        }

        // Handle date/time values
        if (str_contains($key, 'date') || str_contains($key, 'time') || str_contains($key, 'created_at') || str_contains($key, 'updated_at')) {
            try {
                $date = Carbon::parse($value);
                return '<span class="text-muted">' . $date->format('d/m/Y H:i') . '</span>';
            } catch (\Exception $e) {
                return '<span class="text-dark">' . $value . '</span>';
            }
        }

        // Handle email values
        if (str_contains($key, 'email')) {
            return '<a href="mailto:' . $value . '" class="text-decoration-none">' . $value . '</a>';
        }

        // Handle phone values
        if (str_contains($key, 'phone')) {
            return '<a href="tel:' . $value . '" class="text-decoration-none">' . $value . '</a>';
        }

        // Handle URL values
        if (str_contains($key, 'url') || str_contains($key, 'link')) {
            return '<a href="' . $value . '" target="_blank" class="text-decoration-none">' . $value . '</a>';
        }

        // Handle array values
        if (is_array($value)) {
            $html = '<div class="small">';
            foreach ($value as $subKey => $subValue) {
                $html .= '<div class="mb-1"><strong>' . ucfirst($subKey) . ':</strong> ' . $subValue . '</div>';
            }
            $html .= '</div>';
            return $html;
        }

        // Default text value
        return '<span class="text-dark">' . $value . '</span>';
    }

    /**
     * Format status values
     */
    private function formatStatus($status)
    {
        $statusColors = [
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info',
            'no_show' => 'secondary',
            'active' => 'success',
            'inactive' => 'secondary',
            'read' => 'success',
            'unread' => 'warning'
        ];

        $statusLabels = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã hủy',
            'completed' => 'Hoàn thành',
            'no_show' => 'Không đến',
            'active' => 'Hoạt động',
            'inactive' => 'Không hoạt động',
            'read' => 'Đã đọc',
            'unread' => 'Chưa đọc'
        ];

        $color = $statusColors[$status] ?? 'secondary';
        $label = $statusLabels[$status] ?? ucfirst($status);

        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }

    /**
     * Get value type for styling
     */
    private function getValueType($key, $value)
    {
        if (is_numeric($value)) {
            if (in_array($key, ['user_id', 'booking_id', 'note_id', 'review_id', 'ticket_id', 'room_type_id'])) {
                return 'id';
            }
            if (str_contains($key, 'amount') || str_contains($key, 'price') || str_contains($key, 'total')) {
                return 'money';
            }
            return 'number';
        }

        if (is_bool($value)) {
            return 'boolean';
        }

        if (in_array($key, ['new_status', 'old_status', 'status'])) {
            return 'status';
        }

        if (in_array($key, ['booking_code', 'code'])) {
            return 'code';
        }

        if (str_contains($key, 'date') || str_contains($key, 'time')) {
            return 'datetime';
        }

        if (str_contains($key, 'email')) {
            return 'email';
        }

        if (str_contains($key, 'phone')) {
            return 'phone';
        }

        if (str_contains($key, 'url') || str_contains($key, 'link')) {
            return 'url';
        }

        if (is_array($value)) {
            return 'array';
        }

        return 'text';
    }

    /**
     * Get icon for key
     */
    private function getIcon($key)
    {
        $icons = [
            'user_id' => 'fas fa-user',
            'booking_id' => 'fas fa-calendar-check',
            'note_id' => 'fas fa-sticky-note',
            'review_id' => 'fas fa-star',
            'ticket_id' => 'fas fa-headset',
            'room_type_id' => 'fas fa-bed',
            'new_status' => 'fas fa-arrow-right',
            'old_status' => 'fas fa-arrow-left',
            'status' => 'fas fa-info-circle',
            'booking_code' => 'fas fa-barcode',
            'code' => 'fas fa-code',
            'amount' => 'fas fa-money-bill',
            'price' => 'fas fa-tag',
            'total' => 'fas fa-calculator',
            'email' => 'fas fa-envelope',
            'phone' => 'fas fa-phone',
            'name' => 'fas fa-user',
            'title' => 'fas fa-heading',
            'message' => 'fas fa-comment',
            'description' => 'fas fa-align-left',
            'created_at' => 'fas fa-clock',
            'updated_at' => 'fas fa-edit',
            'date' => 'fas fa-calendar',
            'time' => 'fas fa-clock',
            'url' => 'fas fa-link',
            'link' => 'fas fa-external-link-alt',
            'is_read' => 'fas fa-eye',
            'is_active' => 'fas fa-toggle-on',
            'priority' => 'fas fa-exclamation-triangle',
            'type' => 'fas fa-tag',
            'category' => 'fas fa-folder'
        ];

        return $icons[$key] ?? 'fas fa-info-circle';
    }

    /**
     * Get color for key/value combination
     */
    private function getColor($key, $value)
    {
        // Status colors
        if (in_array($key, ['new_status', 'old_status', 'status'])) {
            $statusColors = [
                'pending' => 'warning',
                'confirmed' => 'success',
                'cancelled' => 'danger',
                'completed' => 'info',
                'no_show' => 'secondary'
            ];
            return $statusColors[$value] ?? 'secondary';
        }

        // ID colors
        if (in_array($key, ['user_id', 'booking_id', 'note_id', 'review_id', 'ticket_id', 'room_type_id'])) {
            return 'info';
        }

        // Money colors
        if (str_contains($key, 'amount') || str_contains($key, 'price') || str_contains($key, 'total')) {
            return 'success';
        }

        // Boolean colors
        if (is_bool($value)) {
            return $value ? 'success' : 'danger';
        }

        // Default colors
        $defaultColors = [
            'email' => 'primary',
            'phone' => 'primary',
            'url' => 'primary',
            'link' => 'primary',
            'code' => 'primary',
            'booking_code' => 'primary'
        ];

        return $defaultColors[$key] ?? 'primary';
    }
} 