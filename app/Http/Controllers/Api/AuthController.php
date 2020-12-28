<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserLoginTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
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
        $type = $request->get('type');
        switch ($type) {
            case UserLoginTypeEnum::PASSWORD:
                $username = $request->get('username');
                $password = $request->get('password');
                $result = $this->userService->passwordLogin($username, $password);
                break;
            case UserLoginTypeEnum::CODE:
                $username = $request->get('username');
                $code = $request->get('code');
                $result = $this->userService->smsCodeLogin($username, $code);
                break;
            case UserLoginTypeEnum::MINI_PROGRAM:
                // TODO
                break;
            default:
                return $this->response()->fail('未知的登录方式');
        }

        return $this->response()->success('登录成功', $result);
    }

    /**
     * @param UserRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function register(UserRegisterRequest $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');
        $code = $request->get('code');
        $nickname = $request->get('nickname');
        $user = $this->userService->register($username, $password, $nickname, $code);

        return $this->response()->success('注册成功', $this->userService->login($user));
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
