<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;

class UserService extends Service
{

    /**
     * @param string $authCode
     * @return array
     * @throws \Overtrue\Socialite\Exceptions\AuthorizeFailedException
     */
    public function authCodeLogin(string $authCode)
    {
        /** @var \EasyWeChat\OfficialAccount\Application $app */
        $app = app('wechat.official_account');
        $wechatUser = $app->oauth->userFromCode($authCode);
        $openId = $wechatUser->getId();
        $user = $this->getUserByOpenId($openId);
        if (! $user) {
            $user = $this->register($wechatUser->getNickname(), $wechatUser->getAvatar(), $openId);
        }

        return $this->login($user);
    }

    /**
     * @param User $user
     * @return array
     */
    public function login(User $user)
    {
        $token = 'Bearer ' . auth()->login($user);
        event(new Login('api', $user, false));

        return compact('token', 'user');
    }

    /**
     * @param string $nickname
     * @param string $avatar
     * @param string $openId
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    public function register(string $nickname, string $avatar, string $openId)
    {
        $user = User::create([
            'nickname' => $nickname,
            'avatar' => $avatar,
            'open_id' => $openId,
        ]);
        event(new Registered($user));

        return $user;
    }

    /**
     * return.
     */
    public function logout()
    {
        event(new Logout('api', auth()->guard()->user()));
        auth()->logout();
    }

    /**
     * @param int $id
     * @return User|User[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function getUserById(int $id)
    {
        return User::query()->find($id);
    }

    /**
     * @param string $openId
     * @return User|null
     */
    public function getUserByOpenId(string $openId)
    {
        return User::query()->where('open_id', $openId)->first();
    }
}
