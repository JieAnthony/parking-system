<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;

class UserService extends Service
{

    /**
     * @param string $authCode
     * @return array
     * @throws BusinessException
     */
    public function authCodeLogin(string $authCode): array
    {
        /** @var \EasyWeChat\OfficialAccount\Application $app */
        $app = app('wechat.official_account');
        try {
            $wechatUser = $app->oauth->userFromCode($authCode);
            $openId = $wechatUser->getId();
            $user = $this->getUserByOpenId($openId);
            if (! $user) {
                $user = $this->register($wechatUser->getNickname(), $wechatUser->getAvatar(), $openId);
            }
        } catch (\Exception $exception) {
            throw new BusinessException($exception->getMessage());
        }

        return $this->login($user);
    }

    /**
     * @param User $user
     * @return array
     */
    protected function login(User $user): array
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
     * @return void
     */
    public function logout()
    {
        event(new Logout('api', auth()->guard()->user()));
        auth()->logout();
    }

    /**
     * @param string $openId
     * @return User|null
     */
    public function getUserByOpenId(string $openId): ?User
    {
        return User::query()->where('open_id', $openId)->first();
    }
}
