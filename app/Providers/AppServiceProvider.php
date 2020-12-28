<?php

namespace App\Providers;

use App\Models\Qa;
use App\Models\User;
use App\Observers\QaObserver;
use App\Observers\UserObserver;
use Godruoyi\Snowflake\LaravelSequenceResolver;
use Godruoyi\Snowflake\Snowflake;
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

        User::observe(UserObserver::class);
        Qa::observe(QaObserver::class);
    }
}
