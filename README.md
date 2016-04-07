# laravel5-cloud

>荣联云通讯扩展包，暂时开发了双向回拨电话，等公司企业账号申请完成后，继续测试开发短信功能

[云通讯官方API文档](http://docs.yuntongxun.com/index.php/%E9%A6%96%E9%A1%B5)

Packagist:[https://packagist.org/packages/lancewan/laravel5-cloud](https://packagist.org/packages/lancewan/laravel5-cloud)

## 快速安装

通过`composer`来安装

```php
composer require lancewan/laravel5-cloud
```

或者加入下面代码到 `composer.json` 文件,并执行`composer install` **Or** `composer update`:

```php
"lancewan/laravel5-cloud": "^1.0"
```
扩展包加载完成后,在 `config/app.php` 的文件中加入以下代码:

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

## cloud.php 配置文件
`cloud.php`是云通讯的配置信息:

```php
<?php
return [
	// AccountSid 主帐号
	'accountSid' => '',
	// 主帐号Token
	'accountToken' => '',
	// AppId 应用ID
	'appId' => '',
	// SubAccountSid 子帐号
	'subAccountSid' => '',
	// SubAccountToken 子帐号Token(密码)
	'subAccountToken' => '',
	// VoIPAccount VoIP帐号
	'voIPAccount' => '',
	// VoIPPassword VoIP密码
	'voIPPassword' => '', 
	//请求地址，格式如下，不需要写https://
	'serverIP' => '',
	//请求端口
	'serverPort' => '',
	//REST版本号 云通讯固定版本号，除非官方接口升级外,不要修改版本号
	'softVersion' => '2013-12-26',
	//包体格式，可填值：json 、xml
	'bodyType' => 'json',
	//日志开关。可填值：true、false
	'enabeLog' => true,

	/**
	 * 双向回呼全局配置
	 * @author 晚黎
	 * @date   2016-04-06T15:51:59+0800
	 * @param  [type]   customerSerNum  [被叫侧显示的客服号码]
	 * @param  [type]   fromSerNum      [主叫侧显示的号码]
	 * @param  [type]   promptTone      [自定义回拨提示音]
	 * @param  [type]   alwaysPlay      [第三方私有数据]
	 * @param  [type]   terminalDtmf    [最大通话时长]
	 * @param  [type]   userData        [实时话单通知地址]
	 * @param  [type]   maxCallTime     [是否一直播放提示音]
	 * @param  [type]   hangupCdrUrl    [用于终止播放promptTone参数定义的提示音]
	 * @param  [type]   needBothCdr     [是否给主被叫发送话单]
	 * @param  [type]   needRecord      [是否录音]
	 * @param  [type]   countDownTime   [设置倒计时时间]
	 * @param  [type]   countDownPrompt [倒计时时间到后播放的提示音]
	 *
	 * 不使用全局配置，局部使用需添加额外参数，例如在控制器中调用：
	 * Cloud::callBack($from,$to,$options  = []);
	 * 当传入$options参数时，全局方法不起作用，$options数组中的key对应上面参数说明
	 */
	'callBack' => [
		'customerSerNum'	=> '',
		'fromSerNum' 		=> '',
		'promptTone' 		=> '',
		'alwaysPlay' 		=> '',
		'terminalDtmf' 		=> '',
		'userData' 			=> '',
		'maxCallTime' 		=> '',
		'hangupCdrUrl' 		=> '',
		'needBothCdr' 		=> '',
		'needRecord' 		=> '',
		'countDownTime'		=> '',
		'countDownPrompt' 	=> ''
	]
];
```

## 用法
方法名和云通讯的[测试demo](http://docs.yuntongxun.com/index.php/Demo%E4%B8%8B%E8%BD%BD)中的方法名一致,所以大家要调用什么功能,直接看官方api的方法名来使用本扩展. enjoy it!
### 主账户信息查询
```php
$result = Cloud::queryAccountInfo();
```

## 返回值
调用任何方法后都会返回一个数组,数组中结构如下：
**请求失败**
```php
[
  "status" => "0"
  "errorMsg" => "result error!"
]
```

**验证错误**
```php
[
	'status' => '0' , 
	'errorCode' => '错误码' , 
	'errorMsg' => '错误信息'
]
```

**成功**
```php
[
	'status' => '1' , 
	....
]
```
>成功后的返回值都不同,返回的字段参考官方API文档

完善中...