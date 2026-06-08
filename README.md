# Nhóm 8: Hệ Thống Giám Sát, Phát Hiện Và Ngăn Chặn Truy Cập Đăng Nhập Bất Thường Trên Ứng Dụng Web
## Thành viên

| STT | Họ và tên            | MSSV     |
| --- | -------------------- | -------- |
| 1   | Nguyễn Thị Thùy Linh | 23010633 |
| 2   | Trần Thảo Vy         | 23010588 |
| 3   | Nguyễn Anh Quân      | 23010375 |

## 1. Giới thiệu đề tài

Trong các ứng dụng Web hiện nay, chức năng đăng nhập là mục tiêu tấn công phổ biến của các đối tượng xấu. Các hình thức tấn công như Brute Force Attack, Credential Stuffing hoặc đăng nhập trái phép có thể gây mất an toàn thông tin và ảnh hưởng đến hệ thống.

Đề tài xây dựng một hệ thống đăng nhập an toàn có khả năng phát hiện, ghi nhận và ngăn chặn các hành vi đăng nhập bất thường. Hệ thống cung cấp cơ chế khóa tài khoản, chặn địa chỉ IP, ghi nhật ký bảo mật và dashboard giám sát dành cho quản trị viên.


## 2. Mục tiêu đề tài

* Xây dựng chức năng đăng ký và đăng nhập người dùng.
* Mã hóa mật khẩu bằng thuật toán Hash Password.
* Phát hiện hành vi đăng nhập bất thường.
* Khóa tài khoản khi đăng nhập sai nhiều lần.
* Chặn IP có dấu hiệu tấn công Brute Force.
* Ghi nhận nhật ký bảo mật (Security Logs).
* Xây dựng Security Dashboard phục vụ giám sát hệ thống.
* Áp dụng Role-Based Access Control (RBAC) cho Admin và User.


## 3. Công nghệ sử dụng

| Thành phần            | Công nghệ           |
| --------------------- | ------------------- |
| Ngôn ngữ lập trình    | PHP                 |
| Cơ sở dữ liệu         | SQLite              |
| Frontend              | HTML, CSS           |
| IDE                   | Visual Studio Code  |
| Môi trường phát triển | GitHub Codespaces   |
| Web Server            | PHP Built-in Server |


## 4. Chức năng hệ thống

### 4.1 Đăng ký tài khoản

Người dùng có thể tạo tài khoản mới thông qua giao diện đăng ký. Mật khẩu được mã hóa trước khi lưu vào cơ sở dữ liệu.

### 4.2 Đăng nhập

Hệ thống xác thực thông tin đăng nhập bằng cơ chế Password Hashing và Password Verification.

### 4.3 Đổi mật khẩu

Người dùng có thể thay đổi mật khẩu sau khi đăng nhập thành công.

### 4.4 Quản lý người dùng

Quản trị viên có thể xem danh sách người dùng và quản lý quyền truy cập.

### 4.5 Security Dashboard

Dashboard hiển thị:

* Tổng số người dùng.
* Số lần đăng nhập thành công.
* Số lần đăng nhập thất bại.
* Số tài khoản bị khóa.
* Số IP bị chặn.
* Mức độ rủi ro hiện tại của hệ thống.
* Nhật ký bảo mật gần đây.


## 5. Cơ chế phát hiện đăng nhập bất thường

### 5.1 Brute Force Detection

Hệ thống theo dõi số lần đăng nhập sai liên tiếp.

Nếu số lần đăng nhập sai đạt ngưỡng quy định:

* Tài khoản sẽ bị khóa tạm thời.
* Ghi nhận sự kiện ACCOUNT_LOCKED.

### 5.2 Account Lockout

Sau 5 lần đăng nhập sai liên tiếp:

* Tài khoản bị khóa trong 1 phút.
* Người dùng không thể tiếp tục đăng nhập trong thời gian khóa.

### 5.3 IP Blocking

Hệ thống theo dõi số lượng đăng nhập thất bại từ cùng một địa chỉ IP.

Nếu vượt quá ngưỡng:

* Địa chỉ IP bị chặn tạm thời.
* Ghi nhận sự kiện RATE_LIMIT_EXCEEDED.


## 6. Security Logging

Mọi sự kiện quan trọng đều được lưu vào bảng Security Logs.

Các loại log bao gồm:

| Event Type          | Ý nghĩa              |
| ------------------- | -------------------- |
| LOGIN_SUCCESS       | Đăng nhập thành công |
| LOGIN_FAILED        | Đăng nhập thất bại   |
| LOGIN_BLOCKED       | Đăng nhập bị chặn    |
| ACCOUNT_LOCKED      | Tài khoản bị khóa    |
| RATE_LIMIT_EXCEEDED | Chặn IP              |
| PASSWORD_CHANGED    | Đổi mật khẩu         |

Thông tin được ghi nhận:

* Username
* IP Address
* Event Type
* Severity
* Description
* Event Time

## 7. Phân quyền người dùng (RBAC)

### Admin

Quản trị viên có quyền:

* Xem Security Dashboard.
* Xem danh sách người dùng.
* Quản lý hệ thống.
* Theo dõi các cảnh báo bảo mật.

### User

Người dùng thông thường có quyền:

* Đăng nhập hệ thống.
* Đổi mật khẩu.
* Đăng xuất.

Không được phép truy cập Security Dashboard.


## 8. Security Dashboard

Dashboard cung cấp khả năng giám sát thời gian thực:

### Risk Level

Hệ thống đánh giá mức độ rủi ro:

* LOW
* MEDIUM
* HIGH

Dựa trên:

* Số lần đăng nhập thất bại.
* Số IP bị chặn.
* Các cảnh báo bảo mật gần đây.

### Recent Alerts

Hiển thị các cảnh báo:

* ACCOUNT_LOCKED
* LOGIN_BLOCKED
* RATE_LIMIT_EXCEEDED


## 9. Hướng dẫn chạy dự án

### Clone Repository

```bash
git clone <repository_url>
cd web-login-security-demo
```

### Chạy Web Server

```bash
php -S 0.0.0.0:8000
```

### Truy cập hệ thống

```text
http://localhost:8000
```



## 10. Kết quả đạt được

Hệ thống đã xây dựng thành công:

* Chức năng đăng ký và đăng nhập.
* Mã hóa mật khẩu bằng Password Hashing.
* Phát hiện Brute Force Attack.
* Khóa tài khoản tự động.
* Chặn IP bất thường.
* Security Logging.
* Security Dashboard.
* RBAC Admin/User.

Hệ thống giúp nâng cao khả năng bảo vệ ứng dụng Web trước các hành vi truy cập trái phép và hỗ trợ quản trị viên giám sát an ninh hiệu quả.


## 11. Hướng phát triển

Trong tương lai có thể mở rộng:

* Xác thực hai lớp (2FA).
* Gửi Email Alert khi phát hiện tấn công.
* Tích hợp Google Authenticator.
* Dashboard trực quan bằng biểu đồ.
* Machine Learning phát hiện hành vi bất thường.
* Tích hợp hệ thống SIEM.
