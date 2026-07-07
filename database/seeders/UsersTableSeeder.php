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
                'id' => '1',
                'name' => 'Tuấn Anh',
                'email' => 'giamdoc@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$10$HUnJVfLhK7hwhIiFiItjEuMCmBNFX1KuHcVKHLhBOrfnM37eiYTJa',
                'role_id' => '1',
                'department_id' => NULL,
                'remember_token' => NULL,
                'created_at' => '2026-07-07 02:55:44',
                'updated_at' => '2026-07-07 02:55:44',
            ),
            1 => 
            array (
                'id' => '2',
                'name' => 'Nguyễn Văn A',
                'email' => 'nhanvien.a@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$10$TSqoA.Bm/TGp23TxOn45g.5Yg.UU6rSVTXj/aR4Uf.A2JGZrWjYyK',
                'role_id' => '3',
                'department_id' => '2',
                'remember_token' => NULL,
                'created_at' => '2026-07-07 02:55:44',
                'updated_at' => '2026-07-07 02:55:44',
            ),
            2 => 
            array (
                'id' => '3',
                'name' => 'Trần Thị B',
                'email' => 'nhanvien.b@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$10$ykiqTee4QUeqBvXd2ZzQSONX/T2sFlR5Didg5M/tmIdjHQDafK0He',
                'role_id' => '2',
                'department_id' => '3',
                'remember_token' => NULL,
                'created_at' => '2026-07-07 02:55:44',
                'updated_at' => '2026-07-07 02:55:44',
            ),
        ));
        
        
    }
}