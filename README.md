
ĐỒ ÁN: HỆ THỐNG QUẢN LÝ CÔNG VIỆC (TASK MANAGER)

1. THÔNG TIN NGƯỜI THỰC HIỆN:

Huỳnh Bá Việt Tín - MSSV: 227060140

2. YÊU CẦU HỆ THỐNG MÔI TRƯỜNG:

- XAMPP / MAMP / WAMP (PHP 7.4 hoặc 8.x)
- MySQL / MariaDB
- Trình duyệt khuyên dùng: Google Chrome, Microsoft Edge.

3. HƯỚNG DẪN CÀI ĐẶT & CHẠY THỰC HÀNH:
   Bước 1: Giải nén toàn bộ thư mục source code và copy vào thư mục "htdocs" của XAMPP.
   Bước 2: Bật Apache và MySQL trên bảng điều khiển XAMPP Control Panel.
   Bước 3: Mở trình duyệt, truy cập http://localhost/phpmyadmin
   Bước 4: Tạo một Database mới mang tên "quanly_congviec".
   Bước 5: Chọn tab "Import" (Nhập), duyệt tìm đến file "quanly_congviec.sql" nằm trong thư mục "Database" của source code và bấm thực hiện.
   Bước 6: Truy cập đồ án tại đường dẫn:
   http://localhost/task_manager/public/index.php

4. TÀI KHOẢN TEST SẴN CÓ:

- TÀI KHOẢN ADMIN (Có quyền truy cập bảng Quản trị, cấu hình danh mục):
  - Email: tinhuynhba1289@gmail.com
  - Mật khẩu: 149411

- TÀI KHOẢN USER THƯỜNG (Dùng để test tính năng giao việc nhóm, khóa tài khoản):
  - Email: maydayzzz7879@gmail.com
  - Mật khẩu: 29012004

5. CÁC TÍNH NĂNG NỔI BẬT ĐÁNG CHÚ Ý (Highlight):

- Kiến trúc thư mục chuẩn MVC (Model - View - Controller).
- Không gian làm việc cá nhân & nhóm riêng biệt, tùy chỉnh trạng thái Kanban.
- Dashboard thống kê trực quan (Doughnut Chart, Line Chart) theo thời gian thực.
- Tính năng xuất báo cáo chuyên nghiệp: Xuất file Excel (.csv) và In PDF (CSS tối ưu ẩn menu).
- Hệ thống thông báo Real-time Polling (Quét cảnh báo mà không cần F5 trang).
- Cấu hình hệ thống Động (Dynamic Settings): Admin thêm nhãn, độ ưu tiên, cấu hình SMTP sẽ tự động cập nhật ra giao diện người dùng.
- Hệ thống Đa ngôn ngữ hoàn chỉnh (i18n - Tiếng Việt / Tiếng Anh) toàn diện toàn bộ trang web từ Menu, Cài đặt, Dashboard, Công việc cá nhân cho đến Quản trị Admin và Dự án nhóm.
- Đồng bộ Múi giờ chuẩn xác (Timezone Synchronization) giữa PHP và kết nối cơ sở dữ liệu MySQL (PDO connection timezone), hỗ trợ chuyển đổi linh hoạt theo cấu hình người dùng (VD: Hồ Chí Minh, Tokyo,...).