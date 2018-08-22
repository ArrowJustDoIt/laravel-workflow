<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public static $userArr = [];
    public function __construct()
    {
        self::$userArr = [
            1   =>  'admin',
            2   =>  '分管高管',
            3   =>  '直线主管',
            4   =>  '普通员工',
        ];
    }
}
