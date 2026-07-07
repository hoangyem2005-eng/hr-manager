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
                'id' => 1,
                'TENPHONG' => 'Nhân sự',
                'name' => 'Human Resources',
                'created_at' => '2026-06-22 05:59:49',
                'updated_at' => '2026-07-02 13:04:44',
            ),
            1 => 
            array (
                'id' => 2,
                'TENPHONG' => 'Đào tạo',
                'name' => 'Training',
                'created_at' => '2026-06-22 05:59:49',
                'updated_at' => '2026-07-02 13:04:44',
            ),
            2 => 
            array (
                'id' => 3,
                'TENPHONG' => 'Pháp chế',
                'name' => 'Legal',
                'created_at' => '2026-06-22 05:59:49',
                'updated_at' => '2026-07-02 13:04:44',
            ),
        ));
        
        
    }
}