# Parking-System

## 安装条件

1. PHP >= 7.4
2. **[Composer](https://getcomposer.org/)**
3. swoole、redis拓展

## 安装

```shell
git clone https://github.com/JieAnthony/parking-system
cd parking-system
cp .env.example .env //配置env文件
composer install
php artisan migrate --seed
```

## 管理后台
> http://your.domain/admin

## MQTT订阅
```shell
php artisan mqtt:subscribe
```

## 鸣谢 (排名不分先后)

* swoole
* dcat/laravel-admin
* overtrue/wechat
* simps/mqtt


## License

MIT