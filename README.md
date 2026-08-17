# HỆ THỐNG DOFLOW - QUẢN LÝ CÔNG VIỆC CÁ NHÂN VÀ NHÓM

Dự án phần mềm quản lý công việc và đời sống toàn diện (**DOFLOW**), được thiết kế theo mô hình MVC, hỗ trợ phân tách không gian làm việc cá nhân và quản lý dự án nhóm với bảng Kanban, tích hợp thông báo thời gian thực, nhật ký hoạt động (Audit Log) và cấu hình đa ngôn ngữ chuẩn Enterprise.

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

## CÁC CHỨC NĂNG CÓ TRONG HỆ THỐNG

Hệ thống được chia thành 6 phân hệ (Module) đáp ứng đầy đủ luồng nghiệp vụ thực tế:

### 1. Phân hệ Nền tảng nâng cao (Core Features)
- **Đa ngôn ngữ toàn diện (i18n):** Chuyển đổi linh hoạt giao diện toàn hệ thống giữa Tiếng Việt và Tiếng Anh (bao gồm cả Admin và User).
- **Đồng bộ Múi giờ (Timezone Sync):** Xử lý đồng bộ thời gian giữa PHP và PDO MySQL, cho phép người dùng tùy chỉnh múi giờ làm việc.
- **Cảnh báo tự động (Real-time Polling):** Quét ngầm hệ thống và hiển thị Popup cảnh báo khi có công việc sắp trễ hạn mà không cần tải lại (F5) trang.

### 2. Không gian làm việc cá nhân (Personal Workspace)
- **Bảng Kanban:** Quản lý công việc qua các cột trạng thái (To-do, Doing, Pending, Done).
- **Dashboard Thống kê:** Trực quan hóa dữ liệu bằng Doughnut Chart (tỷ lệ hoàn thành) và Line Chart.
- **Tìm kiếm & Lọc:** Lọc công việc theo từ khóa, trạng thái, danh mục và độ ưu tiên.

### 3. Không gian Cộng tác Nhóm (Team Workspace)
- **Quản lý Đội nhóm:** Khởi tạo nhóm, mời thành viên qua Email, phân quyền chi tiết (Leader, Manager, Member, Viewer) bảo mật tuyệt đối từ Backend.
- **Quản lý Dự án:** Tạo dự án, gán Project Manager, phân công việc (Assignee) trên bảng Kanban Dự án với luồng duyệt nghiêm ngặt (Backlog -> In Progress -> Review -> Done).
- **Kênh Thảo luận & @Mention:** Chat nhóm thời gian thực, bình luận kèm file và tính năng **Tag tên nhắc việc (@Mention)** tự động quét danh sách thành viên kèm hệ thống thông báo.
- **Nhật ký hoạt động (Audit Log):** Ghi nhận vết toàn bộ các thao tác thay đổi, thêm/sửa/xóa task hoặc đổi trạng thái dự án theo thời gian thực.
- **Kết xuất Báo cáo:** Xuất file dự án ra định dạng Excel (.csv) hoặc In báo cáo PDF với CSS tối ưu ẩn giao diện menu.

### 4. Quản trị Hệ thống (Admin Panel)
- **Quản lý Người dùng:** Danh sách tài khoản, phân quyền và khóa/mở khóa tài khoản (Block Account).
- **Cấu hình Động (Dynamic Config):** Tùy chỉnh nhãn, trạng thái, cấu hình hệ thống trực tiếp từ giao diện.
- **Cấu hình SMTP:** Thiết lập máy chủ Email gửi thông báo tự động.

### 5. Quản lý Mục tiêu & Lịch làm việc
- **Mục tiêu (Goals):** Đặt mục tiêu theo thời gian và tự động đo lường % tiến độ dựa trên các Task hoàn thành.
- **Lịch tổng hợp (Master Calendar):** Hiển thị toàn bộ công việc cá nhân và nhóm trên cùng một giao diện lịch tương tác.

### 6. Quản lý Tài khoản (User Identity)
- **Xác thực:** Đăng ký, Đăng nhập, Khôi phục mật khẩu.
- **Hồ sơ:** Cập nhật thông tin liên hệ, Bio và tải lên Avatar cá nhân.

---

## HƯỚNG DẪN CÀI ĐẶT (INSTALLATION)
  
1. Giải nén toàn bộ thư mục source code và đặt vào thư mục `htdocs` của XAMPP.
2. Khởi động dịch vụ **Apache** và **MySQL** trên XAMPP Control Panel.
3. Mở trình duyệt, truy cập vào `http://localhost/phpmyadmin` hoặc sử dụng MySQL Workbench.
4. Tạo một Database mới với tên là `quanly_congviec`.
5. Nhập (Import) dữ liệu từ file `quanly_congviec.sql` nằm trong thư mục `Database` của source code.
6. Khởi chạy ứng dụng tại đường dẫn:
   ```text
   http://localhost/task_manager/public/index.php