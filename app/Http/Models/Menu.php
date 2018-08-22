<?php

namespace App\Http\Models;

class Menu extends Base
{
    //获取tree
    public static function getMenuTree(){
        $menus = self::get()->toArray();
        return self::getTree($menus);
    }

    //获取tree
    protected static function getTree($arr,$pid = 0){
        $tree = array();
        foreach($arr as $k => $v) {
            if($v['parent_id'] == $pid) {
                $v['children'] = static::getTree($arr, $v['id']);
                $tree[] = $v;
            }
        }
        return $tree;
    }
}
