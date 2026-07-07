<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gán cứng tài khoản Giám đốc tối cao
        User::updateOrCreate(
            ['email' => 'giamdoc@gmail.com'], // Nếu trùng email sẽ không bị tạo lặp
            [
                'name' => 'Tuấn Anh',
                'password' => Hash::make(env('DEFAULT_DIRECTOR_PASSWORD', 'change-me')), // Mật khẩu đăng nhập
                'role_id' => 1,                      // Cấp 1: Giám đốc
                'department_id' => null,             // Giám đốc quản lý chung, không thuộc phòng nào
            ]
        );

        $this->call([
            RolesTableSeeder::class,
            DepartmentsTableSeeder::class,
            UsersTableSeeder::class,
        ]);
        $this->call(RolesTableSeeder::class);
        $this->call(DepartmentsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(TasksTableSeeder::class);
        $this->call(ProfilesTableSeeder::class);
        $this->call(DocumentsTableSeeder::class);
        $this->call(NotificationsTableSeeder::class);
        $this->call(HrDocumentsTableSeeder::class);
    }
}
