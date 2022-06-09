<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use API;
use Session;
use DB;
class AccessRightHelper extends ServiceProvider
{
    static public function menu() {

       $module = DB::table('TBM_ROLE_ACCESS as access')
       ->join('TBM_ROLE as role', "role.id", "=", "access.role_id")
       ->join("TBM_MODULE as module", "module.id", "=", "access.module_id")
       ->select('module.id as module_id', 'module.name as module_name', "module.icon as module_icon")
       ->where([
            ["role.id","=",Session::get('role_id')]
       ])
       ->groupBy("module.id")
       ->orderBy("module.sort", "ASC")
       ->get();

       $data = array();
       foreach($module as $row) {
            $menu = DB::table('TBM_ROLE_ACCESS as access')
                ->join('TBM_ROLE as role', "role.id", "=", "access.role_id")
                ->join("TBM_MENU as menu", "menu.id", "=", "access.menu_id")
                ->select('menu.name as name', 'menu.url as url', 'menu.sort')
                ->where([
                    ["role.id", "=", Session::get('role_id')],
                    ["menu.module_id", "=", $row->module_id],
                    ["access.read","=", 1]
                ])
                ->orderBy("menu.sort", "ASC")
                ->get();

            $data[] = array(
                "module" => $row->module_name,
                "module_icon" => $row->module_icon,
                "menu" => $menu
            );    
       }

       return $data;
    }

    static public function granted() {
        $current = str_replace(url('/') . '/', '', url()->current());
        $access = Session::get($current);
        if($access['read']) {
            return true;
        } else {
            return false;
        }
    }

    static public function access() {
        $current = str_replace(url('/') . '/', '', url()->current());
        $operation = Session::get($current);
        return $operation;
    }

    static public function grantAccess() {

        $menu = DB::table('TBM_ROLE_ACCESS as access')
            ->join('TBM_ROLE as role', "role.id", "=", "access.role_id")
            ->join("TBM_MENU as menu", "menu.id", "=", "access.menu_id")
            ->select('menu.name as name', 'menu.url as url', 'menu.sort','access.create', 'access.read', 'access.update', 'access.delete')
            ->where([
                ["role.id", "=", Session::get('role_id')],
            ])
            ->orderBy("menu.sort", "ASC")
            ->get();
                
        foreach ($menu as $row) {
            Session::put($row->url,array(
                'create'=> $row->create,
                'read'=> $row->read,
                'update'=> $row->update,
                'delete'=> $row->delete
            ));
        }     

    }

    static public function profile() {
        $id = Session::get('user_id');
        $profile = DB::table('TBM_USER as user')
            ->select('user.id as id', 'user.img as img', 'user.username', 'role.id as role_id', 'user.name as name', 'role.name as role_name', "user.email as email", "user.job_code", "user.NIK as nik", "user.area_code")
            ->join('TBM_ROLE as role', 'role.id', '=', 'user.role_id')
            ->where([
                ['user.id', '=',  $id],
                ['user.deleted', '=',  '0']
            ])
            ->get();
        
        return (object) $profile;
    }
}
