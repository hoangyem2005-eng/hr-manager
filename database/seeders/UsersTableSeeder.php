<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Hoàng Thị Em',
                'email' => 'em.hoang@mobifone.vn',
                'email_verified_at' => NULL,
                'password' => '$2y$10$rwKVwkVhp0HL2j1p7.Dm0uCYQSrWSyyB9S2h9lV/wyCuLUGNEw6SS',
                'role_id' => 1,
                'department_id' => 1,
                'remember_token' => NULL,
                'created_at' => '2026-06-22 05:59:49',
                'updated_at' => '2026-06-22 05:59:49',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Nguyễn Chí Vinh',
                'email' => 'vinh@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$10$2BgiqHMryVco29YQwN/j/Oo6/IPYdsTKvEEnecijsNXX7X/PLV8H2',
                'role_id' => 2,
                'department_id' => 1,
                'remember_token' => NULL,
                'created_at' => '2026-06-22 06:08:09',
                'updated_at' => '2026-06-22 06:08:09',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Nguyễn Chí Vinh',
                'email' => 'vinhn27045@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$10$tpptchZO1aMY14lmMDIRfuw7OEbZx8vd1awddL/MTyrdkxebZk56q',
                'role_id' => 2,
                'department_id' => 1,
                'remember_token' => NULL,
                'created_at' => '2026-06-22 06:08:41',
                'updated_at' => '2026-06-22 06:08:41',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'HOÀNG YÊM',
                'email' => 'hoangyem2005@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$10$R2m4gapcFUa5rcmd6YpYfuVwa4gOcs38Gfz3gI/9OQDmupgSgKChS',
                'role_id' => 3,
                'department_id' => 1,
                'remember_token' => NULL,
                'created_at' => '2026-06-24 13:45:49',
                'updated_at' => '2026-07-02 13:13:18',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Trần Hoàng Yêm',
                'email' => 'tranthuyaihp2020@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$10$4BGaMUxxhh754qU34dgaxuQ8OyfzVIZ58N69CeYmgD3lXhqMo8yfa',
                'role_id' => 3,
                'department_id' => 1,
                'remember_token' => NULL,
                'created_at' => '2026-06-24 15:13:54',
                'updated_at' => '2026-06-24 15:13:54',
            ),
        ));
        
        
    }
}