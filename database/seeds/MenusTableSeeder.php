<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $menus = [
            [
                'parent_id' => 0,
                'name' => '审批流管理',
                'route'=>'workflows.index',
                'icon' => 'layui-icon-link',
            ],
            [
                'parent_id' => 0,
                'name' => '审批管理',
                'route'=>'',
                'icon' => 'layui-icon-app',
            ],
            [
                'parent_id' => 2,
                'name' => '发起审批',
                'route'=>'approvals.index',
                'icon' => '',
            ],
            [
                'parent_id' => 2,
                'name' => '我的审批',
                'route'=>'approvals.lists',
                'icon' => '',
            ],
            [
                'parent_id' => 2,
                'name' => '待我审批',
                'route'=>'applys.index',
                'icon' => '',
            ],
        ];
        foreach ($menus as $k => $v){
            DB::table('menus')->insert($v);
        }
    }
}
