<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TasksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tasks')->delete();
        
        \DB::table('tasks')->insert(array (
            0 => 
            array (
                'id' => 1,
                'task_name' => 'Cập nhật quy trình tuyển dụng Q3 2025',
                'description' => 'Rà soát và cập nhật quy trình tuyển dụng nhân sự quý 3 năm 2025.',
                'assigned_by' => 1,
                'assigned_to' => 2,
                'deadline' => '2026-06-25',
                'status' => 'Đang làm',
                'created_at' => '2026-06-22 06:02:11',
                'updated_at' => '2026-06-22 06:02:11',
            ),
            1 => 
            array (
                'id' => 2,
                'task_name' => 'Báo cáo KPI tháng 6 phòng Nhân sự',
                'description' => 'Hoàn thiện báo cáo hiệu suất KPI của cả phòng gửi Giám đốc.',
                'assigned_by' => 1,
                'assigned_to' => 3,
                'deadline' => '2026-06-20',
                'status' => 'Quá hạn',
                'created_at' => '2026-06-22 06:02:11',
                'updated_at' => '2026-06-22 06:02:11',
            ),
            2 => 
            array (
                'id' => 3,
                'task_name' => 'Tổ chức đào tạo kỹ năng mềm nội bộ',
                'description' => 'Chuẩn bị phòng họp và slide đào tạo kỹ năng mềm cho chuyên viên.',
                'assigned_by' => 1,
                'assigned_to' => 4,
                'deadline' => '2026-07-07',
                'status' => 'Chờ xử lý',
                'created_at' => '2026-06-22 06:02:11',
                'updated_at' => '2026-06-22 06:02:11',
            ),
            3 => 
            array (
                'id' => 4,
                'task_name' => 'Review hợp đồng lao động mới ký',
                'description' => 'Pháp chế review các điều khoản hợp đồng thử việc mới.',
                'assigned_by' => 1,
                'assigned_to' => 5,
                'deadline' => '2026-06-27',
                'status' => 'Đang review',
                'created_at' => '2026-06-22 06:02:11',
                'updated_at' => '2026-06-22 06:02:11',
            ),
            4 => 
            array (
                'id' => 5,
                'task_name' => 'Cập nhật chính sách phúc lợi nhân viên',
                'description' => 'Bổ sung chính sách bảo hiểm và nghỉ mát hè 2025.',
                'assigned_by' => 1,
                'assigned_to' => 1,
                'deadline' => '2026-06-17',
                'status' => 'Hoàn thành',
                'created_at' => '2026-06-22 06:02:11',
                'updated_at' => '2026-06-22 06:02:11',
            ),
        ));
        
        
    }
}