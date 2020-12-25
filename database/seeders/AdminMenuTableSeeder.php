<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminMenuTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_menu')->delete();
        
        \DB::table('admin_menu')->insert(array (
            0 => 
            array (
                'id' => 1,
                'parent_id' => 0,
                'order' => 1,
                'title' => '欢迎页',
                'icon' => 'feather icon-bar-chart-2',
                'uri' => '/',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'parent_id' => 0,
                'order' => 5,
                'title' => '权限管理',
                'icon' => 'feather icon-settings',
                'uri' => '',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 00:57:55',
            ),
            2 => 
            array (
                'id' => 3,
                'parent_id' => 2,
                'order' => 6,
                'title' => '管理员',
                'icon' => '',
                'uri' => 'auth/users',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 00:57:55',
            ),
            3 => 
            array (
                'id' => 4,
                'parent_id' => 2,
                'order' => 7,
                'title' => '角色',
                'icon' => '',
                'uri' => 'auth/roles',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 00:57:55',
            ),
            4 => 
            array (
                'id' => 5,
                'parent_id' => 2,
                'order' => 8,
                'title' => '权限',
                'icon' => '',
                'uri' => 'auth/permissions',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 00:57:55',
            ),
            5 => 
            array (
                'id' => 6,
                'parent_id' => 2,
                'order' => 9,
                'title' => '菜单',
                'icon' => '',
                'uri' => 'auth/menu',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 00:57:55',
            ),
            6 => 
            array (
                'id' => 7,
                'parent_id' => 2,
                'order' => 10,
                'title' => '扩展',
                'icon' => '',
                'uri' => 'auth/extensions',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 00:57:55',
            ),
            7 => 
            array (
                'id' => 8,
                'parent_id' => 0,
                'order' => 2,
                'title' => '会员管理',
                'icon' => 'fa-users',
                'uri' => 'users',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:11:11',
                'updated_at' => '2020-12-24 23:11:19',
            ),
            8 => 
            array (
                'id' => 9,
                'parent_id' => 0,
                'order' => 3,
                'title' => '级别管理',
                'icon' => 'fa-diamond',
                'uri' => 'levels',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-26 00:46:44',
                'updated_at' => '2020-12-26 00:46:49',
            ),
            9 => 
            array (
                'id' => 10,
                'parent_id' => 0,
                'order' => 4,
                'title' => '常见问题',
                'icon' => 'fa-question-circle',
                'uri' => 'qas',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-26 00:57:50',
                'updated_at' => '2020-12-26 00:58:24',
            ),
        ));
        
        
    }
}