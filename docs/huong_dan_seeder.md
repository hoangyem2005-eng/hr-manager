# Hướng dẫn Kéo (Export) và Đẩy (Import) dữ liệu Database bằng Seeder trong Laravel

Tài liệu này hướng dẫn cách chuyển dữ liệu có sẵn từ Database về máy (dưới dạng file Seeder PHP) và ngược lại nhằm mục đích chia sẻ dữ liệu mẫu, dữ liệu cấu hình giữa các máy phát triển hoặc khi triển khai dự án mà không phải bắt đầu với một database trống.

---

## 1. Quy trình tổng quan

1. **Kéo dữ liệu (Pull / Export)**: Chuyển đổi dữ liệu đang có trong database cục bộ của bạn thành các file Seeder PHP hoặc file dữ liệu (JSON/CSV) nằm trong mã nguồn của dự án.
2. **Đẩy dữ liệu (Push / Import)**: Chạy câu lệnh Artisan để nạp dữ liệu từ các file Seeder vào database của máy khác hoặc môi trường staging/production.

---

## 2. Cách 1: Sử dụng thư viện `orangehill/iseed` (Khuyên dùng - Tự động & Nhanh chóng)

Thư viện `orangehill/iseed` tự động truy vấn dữ liệu từ các bảng trong database và chuyển đổi chúng thành mã mảng PHP đặt trực tiếp vào file Seeder.

### Bước 1: Cài đặt thư viện
Chạy lệnh composer sau ở thư mục gốc của dự án để cài đặt thư viện dưới dạng gói hỗ trợ phát triển (development dependency):

```bash
composer require orangehill/iseed --dev
```

### Bước 2: Xuất dữ liệu (Kéo về máy)
Sử dụng câu lệnh Artisan `iseed` cùng với tên bảng bạn muốn chuyển đổi:

*   **Xuất 1 bảng:**
    ```bash
    php artisan iseed departments
    ```
*   **Xuất nhiều bảng cùng lúc (ngăn cách bằng dấu phẩy):**
    ```bash
    php artisan iseed departments,roles,users
    ```

**Kết quả:**
Thư viện sẽ tạo mới hoặc ghi đè các file Seeder tương ứng tại đường dẫn `database/seeders/`:
*   `database/seeders/DepartmentsTableSeeder.php`
*   `database/seeders/RolesTableSeeder.php`
*   `database/seeders/UsersTableSeeder.php`

Bên trong các file này sẽ chứa mã PHP dạng:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsTableSeeder extends Seeder
{
    public function run()
    {
        // Xóa sạch dữ liệu cũ trong bảng trước khi insert
        DB::table('departments')->delete();
        
        // Đẩy dữ liệu đã được xuất từ DB vào
        DB::table('departments')->insert([
            0 => [
                'id' => 1,
                'name' => 'Ban Giám đốc',
                'created_at' => '2026-07-01 09:00:00',
                'updated_at' => '2026-07-01 09:00:00',
            ],
            1 => [
                'id' => 2,
                'name' => 'Phòng Nhân sự',
                'created_at' => '2026-07-01 09:05:00',
                'updated_at' => '2026-07-01 09:05:00',
            ]
        ]);
    }
}
```

### Bước 3: Đăng ký Seeder
Mở file `database/seeders/DatabaseSeeder.php` và thêm các Seeder vừa tạo vào hàm `run()`:

```php
public function run(): void
{
    $this->call([
        DepartmentsTableSeeder::class,
        RolesTableSeeder::class,
        UsersTableSeeder::class,
    ]);
}
```

---

## 3. Cách 2: Sử dụng dữ liệu JSON thủ công (Không cần cài thêm thư viện)

Nếu không muốn cài đặt thêm các thư viện bên thứ ba, bạn có thể tự quản lý dữ liệu bằng cách xuất file JSON rồi viết seeder đọc file đó.

### Bước 1: Xuất dữ liệu ra file JSON
Sử dụng bất kỳ công cụ quản lý database nào (phpMyAdmin, DBeaver, Beekeeper Studio, Navicat...) chọn xuất bảng (Export table data) thành định dạng **JSON**.

Lưu file này vào dự án của bạn (ví dụ: tạo thư mục `database/data/` và đặt tên là `departments.json`).

### Bước 2: Tạo Seeder đọc file JSON
Chạy lệnh tạo Seeder mới:
```bash
php artisan make:seeder DepartmentSeeder
```

Mở file `database/seeders/DepartmentSeeder.php` mới được tạo và viết logic đọc file JSON:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Xóa sạch dữ liệu cũ của bảng (nếu muốn)
        DB::table('departments')->truncate();

        // 2. Đọc file JSON từ thư mục lưu trữ
        $jsonPath = database_path('data/departments.json');
        if (File::exists($jsonPath)) {
            $json = File::get($jsonPath);
            $data = json_decode($json, true);

            // 3. Nạp dữ liệu vào database
            if (!empty($data)) {
                DB::table('departments')->insert($data);
            }
        }
    }
}
```

---

## 4. Cách nạp dữ liệu (Đẩy dữ liệu vào Database)

Sau khi kéo code có chứa Seeder từ Git/máy khác về, bạn tiến hành nạp dữ liệu vào Database bằng các cách sau:

*   **Chạy toàn bộ Seeder được đăng ký trong `DatabaseSeeder.php`:**
    ```bash
    php artisan db:seed
    ```

*   **Chạy duy nhất một lớp Seeder cụ thể:**
    ```bash
    php artisan db:seed --class=DepartmentsTableSeeder
    ```

*   **Chạy lại toàn bộ Migration (xóa và tạo mới toàn bộ bảng) kèm theo nạp dữ liệu:**
    ```bash
    php artisan migrate:fresh --seed
    ```

---

## 5. Các lưu ý cực kỳ quan trọng

1.  **Thứ tự ưu tiên khi chạy Seeder (Ràng buộc khóa ngoại - Foreign Key):**
    *   Bạn phải chạy Seeder cho các bảng chứa khóa chính trước, rồi mới chạy các bảng tham chiếu khóa ngoại.
    *   *Ví dụ:* Bảng `users` chứa khóa ngoại `department_id` tham chiếu tới bảng `departments`. Bạn bắt buộc phải chạy `DepartmentsTableSeeder` trước rồi mới chạy `UsersTableSeeder`. Nếu thứ tự chạy bị đảo ngược, Laravel sẽ báo lỗi khóa ngoại (`Foreign key constraint violation`).

2.  **Đảm bảo tính Idempotent (Chạy nhiều lần không bị lỗi trùng lặp):**
    *   Mặc định lệnh `iseed` sẽ gọi phương thức `delete()` trên bảng trước khi insert để tránh trùng lặp dữ liệu khi chạy lại.
    *   Nếu bạn tự viết code Seeder và không muốn xóa dữ liệu cũ, hãy dùng hàm `updateOrCreate()` của Eloquent Model thay vì `DB::table()->insert()`:
        ```php
        use App\Models\Department;

        foreach ($data as $item) {
            Department::updateOrCreate(
                ['id' => $item['id']], // Tìm theo khóa chính
                $item                  // Cập nhật hoặc thêm mới dữ liệu tương ứng
            );
        }
        ```

---

## 6. Quy trình phối hợp nhóm (Git Push & Git Pull)

Để dự án hoạt động trơn tru trong nhóm phát triển, dưới đây là quy trình cụ thể cho cả hai bên (Người đẩy dữ liệu lên và Người kéo dữ liệu về).

### BƯỚC A: Người tạo dữ liệu (Bạn - Người xuất Seeder và đưa lên Git)

Khi bạn muốn đưa dữ liệu từ database máy của mình lên Git để người khác dùng:

1.  **Xuất Seeder:** Chạy lệnh `iseed` cho bảng cần thiết (ví dụ: `php artisan iseed departments,roles`).
2.  **Đăng ký Seeder:** Mở file `database/seeders/DatabaseSeeder.php` và thêm các class seeder vừa tạo vào trong hàm `run()`.
3.  **Kiểm tra thay đổi:** Chạy lệnh `git status` để xem các file bị thay đổi.
4.  **Thêm các file cần thiết và Commit:**
    *   Bạn **bắt buộc** phải đẩy các file seeder mới được sinh ra trong thư mục `database/seeders/` lên Git.
    *   Ví dụ các lệnh cần chạy:
        ```bash
        # Thêm các file seeder mới và thay đổi ở DatabaseSeeder
        git add database/seeders/
        
        # Thêm composer.json và composer.lock nếu bạn có cài đặt gói orangehill/iseed
        git add composer.json composer.lock
        
        # Tiến hành commit
        git commit -m "feat: add seeders for departments and roles with initial data"
        
        # Đẩy code lên nhánh làm việc (ví dụ main hoặc develop)
        git push origin main
        ```

---

### BƯỚC B: Người nhận dữ liệu (Đồng nghiệp - Người kéo code về và nạp vào DB)

Khi đồng nghiệp của bạn kéo code mới từ Git về máy của họ, để database của họ có đầy đủ dữ liệu giống bạn, họ cần thực hiện:

1.  **Kéo code mới từ Git:**
    ```bash
    git pull
    ```

2.  **Cập nhật các gói thư viện mới (nếu có):**
    ```bash
    composer install
    ```

3.  **Đẩy dữ liệu vào Database của họ:**
    *   **Trường hợp 1 (Khuyên dùng - Muốn làm mới hoàn toàn database):**
        Lệnh này sẽ xóa sạch database hiện tại của họ, chạy lại toàn bộ migration để tạo lại bảng mới, sau đó tự động nạp toàn bộ dữ liệu từ Seeder.
        ```bash
        php artisan migrate:fresh --seed
        ```
    *   **Trường hợp 2 (Chỉ muốn bổ sung thêm dữ liệu từ các Seeder mới mà không muốn mất dữ liệu đang test khác):**
        Họ có thể chạy lệnh sau để chạy toàn bộ seeder được khai báo:
        ```bash
        php artisan db:seed
        ```
        Hoặc chạy riêng lẻ lớp seeder vừa được thêm:
        ```bash
        php artisan db:seed --class=DepartmentsTableSeeder
        ```

