<?php

namespace James\Geetest;

use Encore\Admin\Admin;
use Encore\Admin\Extension;
use Illuminate\Support\Facades\Route;

class Geetest extends Extension
{
    public static function boot(){
        static::registerRoutes();
        Admin::extend('gesstest', __CLASS__);
    }

    protected static function registerRoutes(){
        parent::routes(function ($router) {
            $router->get('auth/login', 'James\Geetest\GeetestController@index');
            $router->post('auth/login', 'James\Geetest\GeetestController@verify');
        });
        Route::get('/admin/auth/start', 'James\Geetest\GeetestController@start');
    }

}