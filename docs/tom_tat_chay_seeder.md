# Tóm tắt Quy trình Kéo & Đẩy Dữ liệu Seeder (Bản Rút Gọn)

Tài liệu này tóm tắt nhanh các câu lệnh cần chạy cho cả 2 phía: **Người đẩy dữ liệu lên** (khi có thay đổi database cục bộ) và **Người kéo dữ liệu về** (để cập nhật database trên máy khác).

---

## 1. Người Đẩy Dữ Liệu (Đưa dữ liệu mới lên Git)

Khi bạn đã sửa hoặc thêm dữ liệu mới trong database của mình và muốn đưa dữ liệu đó lên cho người khác dùng:

```bash
# Bước 1: Xuất dữ liệu từ các bảng trong database thành file Seeder
php artisan iseed roles,departments,users

# Bước 2: Đẩy các file Seeder và thay đổi lên GitHub
git add database/seeders/ composer.json composer.lock
git commit -m "update: cap nhat du lieu seeder moi nhat"
git push origin yem-feature
```

---

## 2. Người Nhận Dữ Liệu (Kéo code về máy khác và chạy)

Khi đồng nghiệp hoặc máy tính khác kéo code từ Git về, chạy các lệnh sau để nạp dữ liệu vào database cục bộ:

```bash
# Bước 1: Kéo code mới nhất từ GitHub
git pull origin yem-feature

# Bước 2: Đồng bộ hóa thư viện
composer install

# Bước 3: Nạp dữ liệu vào database (Xóa sạch DB cũ và làm mới lại hoàn toàn)
php artisan migrate:fresh --seed
```

*Lưu ý: Nếu chỉ muốn chạy nạp dữ liệu Seeder mới mà không muốn xóa database hiện tại, sử dụng lệnh:*
```bash
php artisan db:seed
```
