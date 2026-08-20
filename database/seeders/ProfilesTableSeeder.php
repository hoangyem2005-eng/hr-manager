<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('profiles')->delete();

        DB::table('profiles')->insert([
            ['id' => 1, 'user_id' => 1, 'employee_code' => 'MBF-0001', 'department' => 'Ban giam doc', 'phone_number' => '0901000001', 'date_of_birth' => '1990-02-14', 'avatar' => null, 'created_at' => '2026-07-01 08:07:44', 'updated_at' => '2026-07-18 17:23:05'],
            ['id' => 2, 'user_id' => 2, 'employee_code' => 'MBF-0002', 'department' => 'Trung tam kinh doanh', 'phone_number' => '0901000002', 'date_of_birth' => '1993-05-09', 'avatar' => null, 'created_at' => '2026-07-01 08:22:18', 'updated_at' => '2026-07-17 16:42:39'],
            ['id' => 3, 'user_id' => 3, 'employee_code' => 'MBF-0003', 'department' => 'Phong vien thong', 'phone_number' => '0901000003', 'date_of_birth' => '1992-11-21', 'avatar' => null, 'created_at' => '2026-07-01 08:37:31', 'updated_at' => '2026-07-16 15:10:54'],
            ['id' => 4, 'user_id' => 4, 'employee_code' => 'MBF-0004', 'department' => 'Phong tong hop', 'phone_number' => '0901000004', 'date_of_birth' => '1994-03-27', 'avatar' => null, 'created_at' => '2026-07-01 09:05:09', 'updated_at' => '2026-07-19 10:16:02'],
            ['id' => 5, 'user_id' => 5, 'employee_code' => 'MBF-0005', 'department' => 'Trung tam kinh doanh', 'phone_number' => '0901000005', 'date_of_birth' => '2001-07-12', 'avatar' => null, 'created_at' => '2026-07-02 07:55:23', 'updated_at' => '2026-07-18 13:29:47'],
            ['id' => 6, 'user_id' => 6, 'employee_code' => 'MBF-0006', 'department' => 'Trung tam kinh doanh', 'phone_number' => '0901000006', 'date_of_birth' => '2000-12-03', 'avatar' => null, 'created_at' => '2026-07-02 08:19:35', 'updated_at' => '2026-07-18 14:05:41'],
            ['id' => 7, 'user_id' => 7, 'employee_code' => 'MBF-0007', 'department' => 'Trung tam kinh doanh', 'phone_number' => '0901000007', 'date_of_birth' => '2002-04-18', 'avatar' => null, 'created_at' => '2026-07-02 08:44:56', 'updated_at' => '2026-07-17 11:38:12'],
            ['id' => 8, 'user_id' => 8, 'employee_code' => 'MBF-0008', 'department' => 'Phong vien thong', 'phone_number' => '0901000008', 'date_of_birth' => '2001-09-25', 'avatar' => null, 'created_at' => '2026-07-03 09:14:19', 'updated_at' => '2026-07-18 08:52:27'],
            ['id' => 9, 'user_id' => 9, 'employee_code' => 'MBF-0009', 'department' => 'Phong vien thong', 'phone_number' => '0901000009', 'date_of_birth' => '2000-01-30', 'avatar' => null, 'created_at' => '2026-07-03 09:41:28', 'updated_at' => '2026-07-19 09:55:30'],
            ['id' => 10, 'user_id' => 10, 'employee_code' => 'MBF-0010', 'department' => 'Phong vien thong', 'phone_number' => '0901000010', 'date_of_birth' => '1999-08-07', 'avatar' => null, 'created_at' => '2026-07-03 10:08:16', 'updated_at' => '2026-07-17 15:47:05'],
            ['id' => 11, 'user_id' => 11, 'employee_code' => 'MBF-0011', 'department' => 'Phong tong hop', 'phone_number' => '0901000011', 'date_of_birth' => '2002-06-16', 'avatar' => null, 'created_at' => '2026-07-04 08:27:22', 'updated_at' => '2026-07-20 10:21:18'],
            ['id' => 12, 'user_id' => 12, 'employee_code' => 'MBF-0012', 'department' => 'Phong tong hop', 'phone_number' => '0901000012', 'date_of_birth' => '2001-10-11', 'avatar' => null, 'created_at' => '2026-07-04 09:01:43', 'updated_at' => '2026-07-19 16:34:55'],
        ]);
    }
}
