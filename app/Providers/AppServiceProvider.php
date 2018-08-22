<?php

namespace App\Providers;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        //左侧菜单
        view()->composer('layout',function($view){
            $menus = \App\Http\Models\Menu::getMenuTree();
            $view->with('menus',$menus);
            $view->with('userArr',\App\Http\Controllers\Controller::$userArr);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
