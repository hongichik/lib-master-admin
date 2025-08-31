# Master Admin

> **Yêu cầu:** Laravel 10, PHP  8.2

**Master Admin** là thư viện Laravel giúp cấu hình nhanh trang quản trị (admin) và một số giao diện người dùng cơ bản, dựa trên giao diện [AdminLTE](https://github.com/ColorlibHQ/AdminLTE#).

## Tính năng

- Tích hợp giao diện AdminLTE hiện đại, dễ tuỳ biến.
- Cấu hình nhanh các trang quản trị cho dự án Laravel.
- Hỗ trợ một số giao diện người dùng mẫu, tiện lợi cho phát triển nhanh.
- Dễ dàng mở rộng và tuỳ chỉnh theo nhu cầu.
- Tích hợp sẵn Google Drive storage để backup và lưu trữ file.

## Cài đặt

**Yêu cầu:** Laravel 10, PHP >= 8.2

### 1. Thêm package từ local path (nếu phát triển hoặc test local)

Mở file `composer.json` của dự án Laravel, thêm vào:

```json
"require": {
	"hongdev/master-admin": "@dev"
},
"repositories": [
	{
		"type": "path",
		"url": "lib-master-admin"
	}
]
```

Sau đó chạy:

```bash
composer update hongdev/master-admin
```

---

## Publish tài nguyên package

Chạy lần lượt các lệnh sau để publish các file cấu hình, public, auth:

```bash
php artisan vendor:publish --provider="Hongdev\MasterAdmin\MasterAdminServiceProvider" --tag=master-admin-environment --force

php artisan vendor:publish --provider="Hongdev\MasterAdmin\MasterAdminServiceProvider" --tag=master-admin-public --force

php artisan vendor:publish --provider="Hongdev\MasterAdmin\MasterAdminServiceProvider" --tag=master-admin-auth --force
```

---

## Cấp quyền thực thi cho các file .sh

Chạy lệnh sau trong thư mục dự án để cấp quyền thực thi cho tất cả file `.sh`:

```bash
find . -type f -name "*.sh" -exec chmod +x {} \;
```

---

## Sử dụng

- Đăng ký ServiceProvider nếu Laravel không tự động phát hiện.
- Truy cập các route mẫu hoặc tuỳ chỉnh theo nhu cầu dự án.

### Cấu hình Google Drive

Thư viện đã tích hợp sẵn Google Drive storage. Chỉ cần thêm các biến môi trường sau vào file `.env`:

```env
FILESYSTEM_CLOUD=google
GOOGLE_DRIVE_CLIENT_ID=your_client_id
GOOGLE_DRIVE_CLIENT_SECRET=your_client_secret
GOOGLE_DRIVE_REFRESH_TOKEN=your_refresh_token
GOOGLE_DRIVE_FOLDER=your_folder_name
```

> **Lưu ý:** Thư viện sẽ tự động refresh access token khi cần thiết. Không cần cài đặt thêm package nào khác.

### Hướng dẫn lấy Google Drive credentials

1. Truy cập [Google Cloud Console](https://console.cloud.google.com)
2. Tạo project mới hoặc chọn project có sẵn
3. Bật Google Drive API
4. Tạo OAuth consent screen (external)
5. Tạo OAuth 2.0 credentials (Web application)
6. Sử dụng client ID và secret được cấp
7. Lấy refresh token thông qua OAuth flow

## Nguồn giao diện

Giao diện sử dụng: [AdminLTE by ColorlibHQ](https://github.com/ColorlibHQ/AdminLTE#)

# lib-master-admin
