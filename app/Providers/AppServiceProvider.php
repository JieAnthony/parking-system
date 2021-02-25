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
