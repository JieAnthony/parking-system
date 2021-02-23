<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Services\UserService;

class AuthController extends Controller
{
    /**
     * @var UserService
     */
    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function login(UserLoginRequest $request)
    {
        $authCode = $request->get('auth_code');

        return $this->response()->success('登录成功', $this->userService->authCodeLogin($authCode));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->userService->logout();

        return $this->response()->success('退出登陆成功');
    }
}
