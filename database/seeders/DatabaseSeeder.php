<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesTableSeeder::class);
        $this->call(DepartmentsTableSeeder::class);
        $this->call(UsersTableSeeder::class);

        // UsersTableSeeder clears the users table, so keep this director account after it runs.
        User::updateOrCreate(
            ['email' => 'giamdoc@gmail.com'],
            [
                'name' => 'Tuan Anh',
                'password' => Hash::make(env('DEFAULT_DIRECTOR_PASSWORD', 'change-me')),
                'role_id' => User::ROLE_ADMIN,
                'department_id' => null,
                'is_active' => true,
            ]
        );

        $this->call(TasksTableSeeder::class);
        $this->call(ProfilesTableSeeder::class);
        $this->call(DocumentsTableSeeder::class);
        $this->call(NotificationsTableSeeder::class);
        $this->call(HrDocumentsTableSeeder::class);
    }
}
