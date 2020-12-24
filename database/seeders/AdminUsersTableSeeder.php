<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminUsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_users')->delete();
        
        \DB::table('admin_users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'username' => 'admin',
                'password' => '$2y$10$SLGnDlrzFE7kCye4WDXWZetN69P.8MsfgnjFnwE3LzXl23se13KIu',
                'name' => 'Administrator',
                'avatar' => NULL,
                'remember_token' => NULL,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-24 23:02:14',
            ),
        ));
        
        
    }
}