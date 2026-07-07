<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('departments')->delete();
        
        \DB::table('departments')->insert(array (
            0 => 
            array (
                'id' => '1',
                'TENPHONG' => 'Ban Giám đốc',
                'name' => 'Director Board',
                'created_at' => '2026-07-07 02:55:44',
                'updated_at' => '2026-07-07 02:55:44',
            ),
            1 => 
            array (
                'id' => '2',
                'TENPHONG' => 'Phòng Nhân sự',
                'name' => 'Human Resources',
                'created_at' => '2026-07-07 02:55:44',
                'updated_at' => '2026-07-07 02:55:44',
            ),
            2 => 
            array (
                'id' => '3',
                'TENPHONG' => 'Phòng Kỹ thuật',
                'name' => 'Technical Department',
                'created_at' => '2026-07-07 02:55:44',
                'updated_at' => '2026-07-07 02:55:44',
            ),
            3 => 
            array (
                'id' => '4',
                'TENPHONG' => 'Phòng Kinh doanh',
                'name' => 'Sales Department',
                'created_at' => '2026-07-07 02:55:44',
                'updated_at' => '2026-07-07 02:55:44',
            ),
        ));
        
        
    }
}