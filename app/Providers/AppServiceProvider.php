<?php

namespace App\Providers;

use App\Models\Qa;
use App\Models\User;
use App\Observers\QaObserver;
use App\Observers\UserObserver;
use Godruoyi\Snowflake\LaravelSequenceResolver;
use Godruoyi\Snowflake\Snowflake;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Overtrue\EasySms\EasySms;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerSingletons();

        $this->registerValidators();

        $this->registerObservers();
    }

    protected function registerSingletons()
    {
        $this->app->singleton('snowflake', function () {
            return (new Snowflake())
                ->setStartTimeStamp(strtotime('2020-01-01') * 1000)
                ->setSequenceResolver(new LaravelSequenceResolver($this->app->get('cache')->store()));
        });
        $this->app->singleton('sms', function () {
            return new EasySms(config('sms'));
        });
        $this->app->singleton('aliPay', function () {
            $config = config('pay.ali');
            $config['notify_url'] = route('api.aliPay.notify');
            if ($this->app->isLocal()) {
                $config['mode'] = 'dev';
                $config['log']['type'] = 'single';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['type'] = 'daily';
                $config['log']['level'] = Logger::INFO;
            }

            return Pay::alipay($config);
        });

        $this->app->singleton('wechatPay', function () {
            $config = config('pay.wechat');
            $config['notify_url'] = route('api.wechatPay.notify');
            if ($this->app->isLocal()) {
                $config['log']['type'] = 'single';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['type'] = 'daily';
                $config['log']['level'] = Logger::INFO;
            }

            return Pay::wechat($config);
        });
    }

    protected function registerObservers()
    {
        User::observe(UserObserver::class);
        Qa::observe(QaObserver::class);
    }

    protected function registerValidators()
    {
        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^1(3[0-9]|4[57]|5[0-35-9]|6[6]|7[0135678]|8[0-9])\d{8}$/', $value);
        });

        Validator::extend('car_license', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新使]{1}[A-Z]{1}[0-9a-zA-Z]{5}$/u', $value);
        });
    }
}
