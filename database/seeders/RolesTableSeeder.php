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
                'id' => '1',
                'name' => 'Giám đốc',
                'created_at' => '2026-07-07 02:55:44',
                'updated_at' => '2026-07-07 02:55:44',
            ),
            1 => 
            array (
                'id' => '2',
                'name' => 'Tổ trưởng',
                'created_at' => '2026-07-07 02:55:44',
                'updated_at' => '2026-07-07 02:55:44',
            ),
            2 => 
            array (
                'id' => '3',
                'name' => 'Nhân viên',
                'created_at' => '2026-07-07 02:55:44',
                'updated_at' => '2026-07-07 02:55:44',
            ),
        ));
        
        
    }
}