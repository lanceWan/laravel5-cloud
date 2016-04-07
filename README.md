# laravel5-cloud

>荣联云通讯扩展包，暂时开发了双向回拨电话，等公司企业账号申请完成后，继续测试开发短信功能

[云通讯官方API文档](http://docs.yuntongxun.com/index.php/%E9%A6%96%E9%A1%B5)

Packagist:[https://packagist.org/packages/lancewan/laravel5-cloud](https://packagist.org/packages/lancewan/laravel5-cloud)

## 快速安装

通过`composer`来安装

```php
composer require lancewan/laravel5-cloud
```

或者加入下面代码到 `composer.json` 文件:

```php
"lancewan/laravel5-cloud": "^1.0"
```
用`composer`加载完成后,在 `config/app.php` 的文件中加入以下代码:

### Service Provider
```php
Lance\Cloud\CloudServiceProvider::class,
```

### Facade
```php
'Cloud'	=> Lance\Cloud\Facades\Cloud::class,
```

最后执行 `php artisan vendor:publish` ,在`config`文件夹下将会生成一个`cloud.php`的配置文件.

And that's it! 

## 用法
方法名和云通讯的[测试demo](http://docs.yuntongxun.com/index.php/Demo%E4%B8%8B%E8%BD%BD)中的方法名一致,所以大家要调用什么功能的时候直接看官方api的方法名就是用本扩展调用. enjoy it!
### 主账户信息查询
完善中...