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
            1 => 'Giám đốc',
            2 => 'Trưởng phòng',
            3 => 'Nhân viên',
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

        DB::table('roles')->whereNotIn('id', [1, 2, 3])->delete();
    }
}
