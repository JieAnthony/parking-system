<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserService extends Service
{
    /**
     * @param $username
     * @param string $password
     * @return array
     * @throws BusinessException
     */
    public function passwordLogin($username, string $password)
    {
        /** @var User $user */
        $user = $this->getUserByUsername($username);
        if (! Hash::check($password, $user->password)) {
            throw new BusinessException('密码错误');
        }

        return $this->login($user);
    }

    /**
     * @param $username
     * @param $code
     * @return array
     * @throws BusinessException
     */
    public function smsCodeLogin($username, $code)
    {
        $this->checkCode($username, $code);
        /** @var User $user */
        $user = $this->getUserByUsername($username);

        return $this->login($user);
    }

    public function miniProgramLogin($jsCode, $iv, $encryptedData)
    {
    }

    /**
     * @param User $user
     * @return array
     */
    public function login(User $user)
    {
        $token = 'Bearer '.auth()->login($user);
        event(new Login('api', $user, false));

        return compact('token', 'user');
    }

    /**
     * @param int $username
     * @param string $password
     * @param string $nickname
     * @param int|null $code
     * @return User|\Illuminate\Database\Eloquent\Model
     * @throws BusinessException
     */
    public function register(int $username, string $password, string $nickname, int $code = null)
    {
        if ($code) {
            $this->checkCode($username, $code);
        }
        $user = User::create([
            'username' => $username,
            'nickname' => $nickname,
            'password' => Hash::make($password),
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
     * @param bool $needCache
     * @return User|User[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function getUserById(int $id, bool $needCache = false)
    {
        if ($needCache) {
            return Cache::rememberForever('user_'.$id, function () use ($id) {
                return User::query()->with(['level'])->findOrFail($id);
            });
        } else {
            return User::query()->with(['level'])->findOrFail($id);
        }
    }

    /**
     * @param $username
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getUserByUsername(int $username)
    {
        return User::query()->where('username', $username)->firstOrFail();
    }

    /**
     * @param $username
     * @param $code
     * @return bool
     * @throws BusinessException
     */
    protected function checkCode(int $username, int $code)
    {
        $smsService = app(SmsService::class);

        return $smsService->check($username, $code);
    }
}
