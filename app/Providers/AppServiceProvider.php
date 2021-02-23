<?php

namespace App\Providers;

use App\Models\Qa;
use App\Models\User;
use App\Observers\QaObserver;
use App\Observers\UserObserver;
use App\Validators\CarLicenseValidator;
use App\Validators\PhoneValidator;
use Godruoyi\Snowflake\LaravelSequenceResolver;
use Godruoyi\Snowflake\Snowflake;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array|string[]
     */
    protected array $validators = [
        'phone' => PhoneValidator::class,
        'car_license' => CarLicenseValidator::class
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSingletons();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerObservers();
        $this->registerValidators();
    }

    protected function registerSingletons()
    {
        $this->app->singleton('snowflake', function () {
            return (new Snowflake())
                ->setStartTimeStamp(strtotime('2020-01-01') * 1000)
                ->setSequenceResolver(new LaravelSequenceResolver($this->app->get('cache')->store()));
        });
        $this->app->singleton('wechatPay', function () {
            $config = config('pay.wechat');
            $config['notify_url'] = route('api.notify.wechat');
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
        foreach ($this->validators as $key => $validator) {
            Validator::extend($key, "{$validator}@validate");
        }
    }
}
