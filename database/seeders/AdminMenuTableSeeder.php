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
                'order' => 10,
                'title' => '权限管理',
                'icon' => 'feather icon-settings',
                'uri' => '',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 19:42:40',
            ),
            2 => 
            array (
                'id' => 3,
                'parent_id' => 2,
                'order' => 11,
                'title' => '管理员',
                'icon' => '',
                'uri' => 'auth/users',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 19:42:40',
            ),
            3 => 
            array (
                'id' => 4,
                'parent_id' => 2,
                'order' => 12,
                'title' => '角色',
                'icon' => '',
                'uri' => 'auth/roles',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 19:42:40',
            ),
            4 => 
            array (
                'id' => 5,
                'parent_id' => 2,
                'order' => 13,
                'title' => '权限',
                'icon' => '',
                'uri' => 'auth/permissions',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 19:42:40',
            ),
            5 => 
            array (
                'id' => 6,
                'parent_id' => 2,
                'order' => 14,
                'title' => '菜单',
                'icon' => '',
                'uri' => 'auth/menu',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 19:42:40',
            ),
            6 => 
            array (
                'id' => 7,
                'parent_id' => 2,
                'order' => 15,
                'title' => '扩展',
                'icon' => '',
                'uri' => 'auth/extensions',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-24 23:02:14',
                'updated_at' => '2020-12-26 19:42:40',
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
                'order' => 9,
                'title' => '常见问题',
                'icon' => 'fa-question-circle',
                'uri' => 'qas',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-26 00:57:50',
                'updated_at' => '2020-12-26 19:42:40',
            ),
            10 => 
            array (
                'id' => 11,
                'parent_id' => 0,
                'order' => 5,
                'title' => '财务明细',
                'icon' => 'fa-calculator',
                'uri' => 'finances',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-26 18:31:41',
                'updated_at' => '2020-12-26 19:28:26',
            ),
            11 => 
            array (
                'id' => 12,
                'parent_id' => 0,
                'order' => 6,
                'title' => '车辆管理',
                'icon' => 'fa-car',
                'uri' => 'cars',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-26 18:31:57',
                'updated_at' => '2020-12-26 19:28:26',
            ),
            12 => 
            array (
                'id' => 13,
                'parent_id' => 0,
                'order' => 8,
                'title' => '车牌字典',
                'icon' => 'fa-file-text',
                'uri' => 'dictionaries',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-26 18:32:35',
                'updated_at' => '2020-12-26 19:42:40',
            ),
            13 => 
            array (
                'id' => 14,
                'parent_id' => 0,
                'order' => 4,
                'title' => '订单管理',
                'icon' => 'fa-reorder',
                'uri' => 'orders',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-26 19:28:19',
                'updated_at' => '2020-12-26 19:28:26',
            ),
            14 => 
            array (
                'id' => 15,
                'parent_id' => 0,
                'order' => 7,
                'title' => '道闸管理',
                'icon' => 'fa-sliders',
                'uri' => 'barriers',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-26 19:42:33',
                'updated_at' => '2020-12-26 19:42:40',
            ),
            15 => 
            array (
                'id' => 16,
                'parent_id' => 0,
                'order' => 16,
                'title' => '系统设置',
                'icon' => 'fa-gears',
                'uri' => '',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-26 20:07:18',
                'updated_at' => '2020-12-26 20:07:18',
            ),
            16 => 
            array (
                'id' => 17,
                'parent_id' => 16,
                'order' => 17,
                'title' => '基础信息',
                'icon' => '',
                'uri' => 'system/info',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-26 20:13:34',
                'updated_at' => '2020-12-26 20:18:10',
            ),
            17 => 
            array (
                'id' => 18,
                'parent_id' => 16,
                'order' => 18,
                'title' => '扣费方式',
                'icon' => '',
                'uri' => 'system/deduction',
                'extension' => '',
                'show' => 1,
                'created_at' => '2020-12-26 20:14:02',
                'updated_at' => '2020-12-26 21:16:09',
            ),
        ));
        
        
    }
}