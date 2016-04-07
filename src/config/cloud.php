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
	//REST版本号
	'softVersion' => '',
	//包体格式，可填值：json 、xml
	'bodyType' => 'xml',
	//日志开关。可填值：true
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
	 * 不适用全局配置，局部使用额外参数的，例如在控制器中调用：
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