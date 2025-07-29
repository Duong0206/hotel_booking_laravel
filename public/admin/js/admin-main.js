// Admin Panel Main JavaScript

// CSRF Token cho AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Hệ thống thông báo thời gian thực - Phiên bản đơn giản
// XÓA TOÀN BỘ CLASS NotificationManager VÀ CÁC HÀM LIÊN QUAN ĐẾN THÔNG BÁO, AJAX, TEST NOTIFICATION, MARK AS READ, LOAD NOTIFICATIONS, POLLING...
// Chỉ giữ lại các hàm hiệu ứng giao diện, animation, sidebar, button, toast, v.v.

// Animation cho cards
function initCardAnimations() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 * index);
    });
}

// Hover effect cho buttons
function initButtonEffects() {
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
}

// Toggle sidebar
function initSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const topbar = document.querySelector('.topbar');
    
    if (sidebarToggle && sidebar && mainContent && topbar) {
        sidebarToggle.addEventListener('click', function() {
            // Toggle sidebar
            sidebar.classList.toggle('collapsed');
            
            // Toggle main content
            mainContent.classList.toggle('expanded');
            
            // Toggle topbar
            topbar.classList.toggle('expanded');
            
            // Update toggle button icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.className = 'fas fa-bars';
                this.title = 'Show Sidebar';
            } else {
                icon.className = 'fas fa-times';
                this.title = 'Hide Sidebar';
            }
            
            // Save state to localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
        
        // Restore state from localStorage
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            topbar.classList.add('expanded');
            const icon = sidebarToggle.querySelector('i');
            icon.className = 'fas fa-bars';
            sidebarToggle.title = 'Show Sidebar';
        }
    }
}

// Đóng sidebar khi click bên ngoài trên mobile
function initSidebarClose() {
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        if (window.innerWidth <= 992 && sidebar.classList.contains('show')) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        }
    });
}

// Xử lý window resize
function initWindowResize() {
    window.addEventListener('resize', function() {
        const sidebar = document.querySelector('.sidebar');
        if (window.innerWidth > 992) {
            sidebar.classList.remove('show');
        }
    });
}

// Cải thiện dropdown positioning trên mobile
function initDropdownPositioning() {
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');
        if (menu) {
            dropdown.addEventListener('show.bs.dropdown', function() {
                const rect = menu.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                
                if (rect.right > viewportWidth) {
                    menu.style.left = 'auto';
                    menu.style.right = '0';
                }
            });
        }
    });
}

// Đóng dropdown khi click bên ngoài
function initDropdownClose() {
    document.addEventListener('click', function(e) {
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(e.target) && !dropdown.previousElementSibling.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    });
}

// Khởi tạo tất cả các chức năng khi trang đã load
$(document).ready(function() {
    console.log('Document ready: Starting initialization...');
    
    // Khởi tạo các animation và effects
    if (typeof initCardAnimations === 'function') initCardAnimations();
    if (typeof initButtonEffects === 'function') initButtonEffects();
    if (typeof initSidebarToggle === 'function') initSidebarToggle();
    if (typeof initSidebarClose === 'function') initSidebarClose();
    if (typeof initWindowResize === 'function') initWindowResize();
    if (typeof initDropdownPositioning === 'function') initDropdownPositioning();
    if (typeof initDropdownClose === 'function') initDropdownClose();
    
    // Auto-hide alerts sau 5 giây
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
    
    // Smooth scroll cho anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Popover initialization
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// Utility functions
window.AdminUtils = {
    // Hiển thị toast notification
    showToast: function(message, type = 'info') {
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);

        $('.toast-container').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();

        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    },
    
    // Confirm dialog
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    },
    
    // Format currency
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    },
    
    // Format date
    formatDate: function(date) {
        return new Intl.DateTimeFormat('vi-VN').format(new Date(date));
    },
    
    // Loading state
    showLoading: function(element) {
        $(element).addClass('loading');
    },
    
    hideLoading: function(element) {
        $(element).removeClass('loading');
    }
}; 