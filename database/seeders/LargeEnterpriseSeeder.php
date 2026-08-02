<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Models\Task;
use App\Models\Notification;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LargeEnterpriseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo 10 phòng ban
        $depts = [
            'Trung tâm kinh doanh', 'Phòng viễn thông', 'Phòng tổng hợp', 'Kỹ thuật', 
            'Kinh doanh', 'Marketing', 'Tài chính', 
            'Kế hoạch', 'Dịch vụ khách hàng', 'Công nghệ thông tin'
        ];
        
        $deptModels = [];
        foreach ($depts as $name) {
            $deptModels[] = Department::firstOrCreate(
                ['TENPHONG' => $name],
                ['name' => Str::slug($name)]
            );
        }

        // 2. Tạo hoặc đảm bảo có sẵn các Role
        $roleAdmin = Role::firstOrCreate(['id' => 1], ['name' => 'Admin']);
        $roleLeader = Role::firstOrCreate(['id' => 2], ['name' => 'Quản lý']);
        $roleEmployee = Role::firstOrCreate(['id' => 3], ['name' => 'Nhân viên']);

        // 3. Đảm bảo có tài khoản Giám đốc và Trưởng phòng mẫu
        $admin = User::firstOrCreate(
            ['email' => 'giamdoc@gmail.com'],
            [
                'name' => 'Tuấn Anh',
                'password' => Hash::make(env('DEFAULT_DIRECTOR_PASSWORD', 'change-me')),
                'role_id' => 1,
                'department_id' => null,
            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'truongphong@gmail.com'],
            [
                'name' => 'Nguyễn Trưởng Phòng',
                'password' => Hash::make(env('DEFAULT_USER_PASSWORD', 'change-me')),
                'role_id' => 2,
                'department_id' => $deptModels[0]->id,
            ]
        );

        // Tạo thêm 14 quản lý cho các phòng ban khác
        $managers = [$manager];
        for ($i = 2; $i <= 15; $i++) {
            $managers[] = User::firstOrCreate(
                ['email' => "manager{$i}@gmail.com"],
                [
                    'name' => "Quản lý Phòng " . $i,
                    'password' => Hash::make('password123'),
                    'role_id' => 2,
                    'department_id' => $deptModels[$i % 10]->id,
                ]
            );
        }

        // Tạo thêm 85 nhân viên ngẫu nhiên phân bổ vào các phòng ban
        $employees = [];
        $firstNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Phan', 'Vũ', 'Đặng', 'Bùi', 'Đỗ'];
        $midNames = ['Văn', 'Thị', 'Quốc', 'Thanh', 'Đức', 'Minh', 'Ngọc', 'Hữu', 'Công', 'Phương'];
        $lastNames = ['Anh', 'Bình', 'Chi', 'Dũng', 'Em', 'Giang', 'Hải', 'Khánh', 'Linh', 'Nam', 'Oanh', 'Phúc', 'Quỳnh', 'Sơn', 'Trang', 'Tuấn', 'Việt', 'Yến'];

        for ($i = 1; $i <= 85; $i++) {
            $name = $firstNames[array_rand($firstNames)] . ' ' 
                  . $midNames[array_rand($midNames)] . ' ' 
                  . $lastNames[array_rand($lastNames)];
            
            $employees[] = User::firstOrCreate(
                ['email' => "nhanvien{$i}@gmail.com"],
                [
                    'name' => $name,
                    'password' => Hash::make('password123'),
                    'role_id' => 3,
                    'department_id' => $deptModels[array_rand($deptModels)]->id,
                ]
            );
        }

        // 4. Tạo 500 tasks ngẫu nhiên
        $taskNames = [
            'Rà soát báo cáo kế hoạch Q2', 'Lên kế hoạch tuyển dụng nhân sự mới',
            'Sửa lỗi giao diện màn hình đăng nhập', 'Tối ưu hóa truy vấn cơ sở dữ liệu',
            'Thiết lập hàng rào bảo mật download tài liệu', 'Viết tài liệu hướng dẫn sử dụng API',
            'Kiểm thử tích hợp các module cốt lõi', 'Đồng bộ dữ liệu sang server staging',
            'Họp giao ban phòng ban đầu tuần', 'Review mã nguồn và phê duyệt PR',
            'Cập nhật chính sách bảo mật nội bộ', 'Khảo sát mức độ hài lòng của nhân viên',
            'Lập ngân sách hoạt động năm tới', 'Thiết kế chiến dịch marketing tháng mới',
            'Xử lý phản hồi của khách hàng VIP'
        ];

        $statuses = ['Chờ xử lý', 'Đang làm', 'Đang review', 'Hoàn thành', 'Quá hạn'];
        $priorities = ['Thấp', 'Trung bình', 'Cao'];

        for ($i = 1; $i <= 500; $i++) {
            $status = $statuses[array_rand($statuses)];
            $assigned_to = $employees[array_rand($employees)];
            $assigned_by = $managers[array_rand($managers)];
            
            $deadline = Carbon::now();
            if ($status === 'Quá hạn') {
                $deadline = $deadline->subDays(rand(1, 10));
            } else {
                $deadline = $deadline->addDays(rand(1, 30));
            }

            $progress = 0;
            if ($status === 'Hoàn thành') {
                $progress = 100;
            } elseif ($status === 'Đang làm') {
                $progress = rand(10, 80);
            } elseif ($status === 'Đang review') {
                $progress = rand(80, 95);
            }

            Task::create([
                'task_name' => $taskNames[array_rand($taskNames)] . " #" . $i,
                'description' => "Mô tả chi tiết cho công việc giao cho nhân viên " . $assigned_to->name . ". Yêu cầu hoàn thành trước thời hạn đề ra.",
                'assigned_by' => $assigned_by->id,
                'assigned_to' => $assigned_to->id,
                'deadline' => $deadline,
                'priority' => $priorities[array_rand($priorities)],
                'status' => $status,
                'progress' => $progress,
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
