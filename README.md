# HỆ THỐNG QUẢN LÝ CÔNG VIỆC VÀ CỘNG TÁC NHÓM (TASK MANAGER)

Dự án phần mềm quản lý công việc toàn diện, được thiết kế theo mô hình MVC, hỗ trợ phân tách không gian làm việc cá nhân và quản lý dự án nhóm với bảng Kanban, tích hợp thống kê thời gian thực và cấu hình đa ngôn ngữ chuẩn Enterprise.

---

## THÔNG TIN SINH VIÊN THỰC HIỆN
- **Họ và tên:** Huỳnh Bá Việt Tín
- **Mã số sinh viên:** 227060140

---

## MÔI TRƯỜNG & CÔNG NGHỆ SỬ DỤNG
- **Kiến trúc phần mềm:** Mô hình MVC (Model - View - Controller).
- **Ngôn ngữ & Cơ sở dữ liệu:** PHP (7.4 hoặc 8.x) & MySQL / MariaDB (Kết nối PDO an toàn).
- **Môi trường triển khai:** XAMPP / MAMP / WAMP (Localhost).
- **Trình duyệt khuyên dùng:** Google Chrome, Microsoft Edge.

---

## CÁC CHỨC NĂNG CÓ TRONG WEB

Hệ thống được chia thành 6 phân hệ (Module) đáp ứng đầy đủ luồng nghiệp vụ thực tế:

### 1. Phân hệ Nền tảng nâng cao (Core Features)[cite: 30]
- **Đa ngôn ngữ toàn diện (i18n):** Chuyển đổi linh hoạt giao diện toàn hệ thống giữa Tiếng Việt và Tiếng Anh (bao gồm cả Admin và User)[cite: 30].
- **Đồng bộ Múi giờ (Timezone Sync):** Xử lý đồng bộ thời gian giữa PHP và PDO MySQL, cho phép người dùng tùy chỉnh múi giờ làm việc (VD: Hồ Chí Minh, Tokyo)[cite: 30].
- **Cảnh báo tự động (Real-time Polling):** Quét ngầm hệ thống và hiển thị Popup cảnh báo khi có công việc sắp trễ hạn mà không cần tải lại (F5) trang[cite: 30].

### 2. Không gian làm việc cá nhân (Personal Workspace)[cite: 30]
- **Bảng Kanban:** Quản lý công việc qua các cột trạng thái (To-do, Doing, Pending, Done)[cite: 30].
- **Dashboard Thống kê:** Trực quan hóa dữ liệu bằng Doughnut Chart (tỷ lệ hoàn thành) và Line Chart (dự báo khối lượng công việc)[cite: 30].
- **Tìm kiếm & Lọc:** Lọc công việc theo từ khóa, trạng thái, danh mục và độ ưu tiên.

### 3. Không gian Cộng tác Nhóm (Team Workspace)[cite: 30]
- **Quản lý Đội nhóm:** Khởi tạo nhóm, mời thành viên qua Email, phân quyền Leader/Member và giải tán nhóm[cite: 30].
- **Quản lý Dự án:** Tạo dự án, gán Project Manager, phân công việc (Assignee) trên bảng Kanban Dự án với luồng duyệt nghiêm ngặt (In Progress -> Review -> Done)[cite: 30].
- **Kênh Thảo luận:** Chat nhóm thời gian thực (Team Chat) và bình luận đính kèm file trong từng thẻ công việc (Task Discussion).
- **Kết xuất Báo cáo:** Xuất file dự án ra định dạng Excel (.csv) hoặc In báo cáo PDF với CSS tối ưu ẩn giao diện menu[cite: 30].

### 4. Quản trị Hệ thống (Admin Panel)[cite: 30]
- **Quản lý Người dùng:** Danh sách tài khoản, phân quyền và khóa/mở khóa tài khoản (Block Account)[cite: 30].
- **Cấu hình Động (Dynamic Config):** Admin có thể tùy chỉnh Nhãn công việc, Trạng thái, Độ ưu tiên trực tiếp từ giao diện và lưu vào Database[cite: 30].
- **Cấu hình SMTP:** Thiết lập máy chủ Email gửi thông báo tự động[cite: 30].

### 5. Quản lý Mục tiêu & Lịch làm việc
- **Mục tiêu (Goals):** Đặt mục tiêu theo Tuần/Tháng/Quý và tự động đo lường % tiến độ dựa trên các Task hoàn thành.
- **Lịch tổng hợp (Master Calendar):** Hiển thị toàn bộ công việc cá nhân và nhóm trên cùng một giao diện lịch tương tác.

### 6. Quản lý Tài khoản (User Identity)
- **Xác thực:** Đăng ký, Đăng nhập, Khôi phục mật khẩu.
- **Hồ sơ:** Cập nhật thông tin liên hệ, giới thiệu (Bio) và tải lên Avatar.

---

## HƯỚNG DẪN CÀI ĐẶT (INSTALLATION)
  
1. Giải nén toàn bộ thư mục source code và đặt vào thư mục `htdocs` của XAMPP[cite: 30].
2. Khởi động dịch vụ **Apache** và **MySQL** trên XAMPP Control Panel[cite: 30].
3. Mở trình duyệt, truy cập vào `http://localhost/phpmyadmin`[cite: 30].
4. Tạo một Database mới với tên là `quanly_congviec`[cite: 30].
5. Chọn tab **Import** (Nhập), duyệt tìm đến file `quanly_congviec.sql` nằm trong thư mục `Database` của source code và bấm thực hiện[cite: 30].
6. Khởi chạy ứng dụng tại đường dẫn:
   ```text
   http://localhost/task_manager/public/index.php