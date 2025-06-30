# Master Admin

**Master Admin** là thư viện Laravel giúp cấu hình nhanh trang quản trị (admin) và một số giao diện người dùng cơ bản, dựa trên giao diện [AdminLTE](https://github.com/ColorlibHQ/AdminLTE#).

## Tính năng

- Tích hợp giao diện AdminLTE hiện đại, dễ tuỳ biến.
- Cấu hình nhanh các trang quản trị cho dự án Laravel.
- Hỗ trợ một số giao diện người dùng mẫu, tiện lợi cho phát triển nhanh.
- Dễ dàng mở rộng và tuỳ chỉnh theo nhu cầu.

## Cài đặt

```bash
composer require hongdev/master-admin
```

## Sử dụng

- Đăng ký ServiceProvider nếu Laravel không tự động phát hiện.
- Truy cập các route mẫu hoặc tuỳ chỉnh theo nhu cầu dự án.

### Lưu ý khi sử dụng Google Drive

Để sử dụng Google Drive làm disk, cần cài đặt thêm package:

```bash
composer require yaza/laravel-google-drive-storage
```

Sau đó cấu hình trong file `config/filesystems.php` như sau:

```php
'disks' => [
    // ...existing code...
    'google' => [
        'driver' => 'google',
        'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
        'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'accessToken' => env('GOOGLE_DRIVE_ACCESS_TOKEN'), // optional
        'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
        'folder' => env('GOOGLE_DRIVE_FOLDER'),
    ],
],
'cloud' => env('FILESYSTEM_CLOUD', 'google'),
```

> **Lưu ý:** Nếu gặp lỗi `Disk [google] does not have a configured driver`, hãy chắc chắn đã:
> - Thêm cấu hình disk `google` vào file `config/filesystems.php` như trên.
> - Đã cài đặt package `yaza/laravel-google-drive-storage`.
> - Đặt biến môi trường `FILESYSTEM_CLOUD=google` trong file `.env`.
> - Chạy lại lệnh `php artisan config:clear` để Laravel nhận cấu hình mới.

## Nguồn giao diện

Giao diện sử dụng: [AdminLTE by ColorlibHQ](https://github.com/ColorlibHQ/AdminLTE#)
# lib-master-admin
