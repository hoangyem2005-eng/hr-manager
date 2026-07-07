<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Giám đốc / Phó Giám đốc',
                'created_at' => '2026-06-22 05:59:49',
                'updated_at' => '2026-07-02 13:04:44',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Trưởng phòng / Tổ trưởng',
                'created_at' => '2026-06-22 05:59:49',
                'updated_at' => '2026-07-02 13:04:44',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Nhân viên',
                'created_at' => '2026-06-22 05:59:49',
                'updated_at' => '2026-06-22 05:59:49',
            ),
        ));
        
        
    }
}