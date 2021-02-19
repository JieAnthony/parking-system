<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WeChatController extends Controller
{
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        $app = app('wechat.official_account');
        $app->server->push(function ($message) {
            return "欢迎关注！";
        });

        return $app->server->serve();
    }

    public function test()
    {
        $app = app('wechat.official_account');
        echo $app->oauth->redirect();
    }
}
