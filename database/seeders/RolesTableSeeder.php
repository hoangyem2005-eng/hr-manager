<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        foreach ([
            1 => 'Phụ trách chi nhánh',
            2 => 'Phó giám đốc chi nhánh',
            3 => 'Nhân viên',
            4 => 'Giám đốc trung tâm kinh doanh',
            5 => 'Phó giám đốc trung tâm kinh doanh',
            6 => 'Phụ trách phòng viễn thông',
            7 => 'Phụ trách phòng tổng hợp',
        ] as $id => $name) {
            DB::table('roles')->updateOrInsert(
                ['id' => $id],
                [
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        DB::table('roles')->whereNotIn('id', [1, 2, 3, 4, 5, 6, 7])->delete();
    }
}
