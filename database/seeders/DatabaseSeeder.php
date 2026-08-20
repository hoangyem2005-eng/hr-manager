<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            $this->call(RolesTableSeeder::class);
            $this->call(DepartmentsTableSeeder::class);
            $this->call(UsersTableSeeder::class);
            $this->call(TasksTableSeeder::class);
            $this->call(ProfilesTableSeeder::class);
            $this->call(DocumentsTableSeeder::class);
            $this->call(NotificationsTableSeeder::class);
            $this->call(HrDocumentsTableSeeder::class);
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
}
