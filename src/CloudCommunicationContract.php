<?php
namespace Lance\Cloud;
/**
 * 云通讯接口
 */
interface CloudCommunicationContract{
	/**
	 * 打印日志
	 * @author 晚黎
	 * @date   2016-04-06T15:45:01+0800
	 * @param  [type]                   $log [日志内容]
	 * @return [type]                        [description]
	 */
	public function showlog($log);
	/**
	 * 发起HTTPS请求
	 * @author 晚黎
	 * @date   2016-04-06T15:45:47+0800
	 * @param  [type]                   $url    [请求地址]
	 * @param  [type]                   $data   [请求包体]
	 * @param  [type]                   $header [请求包头]
	 * @param  integer                  $post   [请求方式 默认为1 1：post，0：get]
	 * @return [type]                           [description]
	 */
	public function curl_post($url,$data,$header,$post=1);
	/**
	 * 创建子帐号
	 * @author 晚黎
	 * @date   2016-04-06T15:47:12+0800
	 * @param  [type]                   $friendlyName [子帐号名称]
	 * @return [type]                                 [description]
	 */
	public function createSubAccount($friendlyName);

	/**
	 * 获取子帐号
	 * @author 晚黎
	 * @date   2016-04-06T15:48:30+0800
	 * @param  [type]                   $startNo [开始的序号，默认从0开始]
	 * @param  [type]                   $offset  [一次查询的最大条数，最小是1条，最大是100条]
	 * @return [type]                            [description]
	 */
	public function getSubAccounts($startNo,$offset);

	/**
	 * 子帐号信息查询
	 * @author 晚黎
	 * @date   2016-04-06T15:50:11+0800
	 * @param  [type]                   $friendlyName [子帐号名称]
	 * @return [type]                                 [description]
	 */
	public function querySubAccount($friendlyName);

	/**
	 * 发送模板短信
	 * @author 晚黎
	 * @date   2016-04-06T15:50:58+0800
	 * @param  [type]                   $to     [短信接收彿手机号码集合,用英文逗号分开]
	 * @param  [type]                   $datas  [内容数据]
	 * @param  [type]                   $tempId [模板Id]
	 * @return [type]                           [description]
	 */
	public function sendTemplateSMS($to,$datas,$tempId);

	/**
	 * 双向回呼
	 * @author 晚黎
	 * @date   2016-04-06T15:51:59+0800
	 * @param  [type]	$from            [主叫电话号码]
	 * @param  [type]	$to              [被叫电话号码]
	 * $options数组中的key：
	 * @param  [type]	customerSerNum  [被叫侧显示的客服号码]
	 * @param  [type]	fromSerNum      [主叫侧显示的号码]
	 * @param  [type]	promptTone      [自定义回拨提示音]
	 * @param  [type]	alwaysPlay      [第三方私有数据]
	 * @param  [type]	terminalDtmf    [最大通话时长]
	 * @param  [type]	userData        [实时话单通知地址]
	 * @param  [type]	maxCallTime     [是否一直播放提示音]
	 * @param  [type]	hangupCdrUrl    [用于终止播放promptTone参数定义的提示音]
	 * @param  [type]	needBothCdr     [是否给主被叫发送话单]
	 * @param  [type]	needRecord      [是否录音]
	 * @param  [type]	countDownTime   [设置倒计时时间]
	 * @param  [type]	countDownPrompt [倒计时时间到后播放的提示音]
	 */
	public function callBack($from,$to,$options=[]);

	/**
	 * 外呼通知
	 * @author 晚黎
	 * @date   2016-04-06T15:53:57+0800
	 * @param  [type]                   $to          [被叫号码]
	 * @param  [type]                   $mediaName   [语音文件名称，格式 wav。与mediaTxt不能同时为空。当不为空时mediaTxt属性失效。]
	 * @param  [type]                   $mediaTxt    [文本内容]
	 * @param  [type]                   $displayNum  [显示的主叫号码]
	 * @param  [type]                   $playTimes   [循环播放次数，1－3次，默认播放1次。]
	 * @param  [type]                   $respUrl     [外呼通知状态通知回调地址，云通讯平台将向该Url地址发送呼叫结果通知。]
	 * @param  [type]                   $userData    [用户私有数据]
	 * @param  [type]                   $maxCallTime [最大通话时长]
	 * @param  [type]                   $speed       [发音速度]
	 * @param  [type]                   $volume      [音量]
	 * @param  [type]                   $pitch       [音调]
	 * @param  [type]                   $bgsound     [背景音编号]
	 * @return [type]                                [description]
	 */
	public function landingCall($to,$mediaName,$mediaTxt,$displayNum,$playTimes,$respUrl,$userData,$maxCallTime,$speed,$volume,$pitch,$bgsound);

	/**
	 * 语音验证码
	 * @author 晚黎
	 * @date   2016-04-06T15:55:15+0800
	 * @param  [type]                   $verifyCode [验证码内容，为数字和英文字母，不区分大小写，长度4-8位]
	 * @param  [type]                   $playTimes  [播放次数，1－3次]
	 * @param  [type]                   $to         [接收号码]
	 * @param  [type]                   $displayNum [显示的主叫号码]
	 * @param  [type]                   $respUrl    [语音验证码状态通知回调地址，云通讯平台将向该Url地址发送呼叫结果通知]
	 * @param  [type]                   $lang       [语言类型]
	 * @param  [type]                   $userData   [第三方私有数据]
	 * @return [type]                               [description]
	 */
	public function voiceVerify($verifyCode,$playTimes,$to,$displayNum,$respUrl,$lang,$userData);

	/**
	 * IVR外呼
	 * @author 晚黎
	 * @date   2016-04-06T15:56:20+0800
	 * @param  [type]                   $number   [待呼叫号码，为Dial节点的属性]
	 * @param  [type]                   $userdata [用户数据，在<startservice>通知中返回，只允许填写数字字符，为Dial节点的属性]
	 * @param  [type]                   $record   [是否录音，可填项为true和false，默认值为false不录音，为Dial节点的属性]
	 * @return [type]                             [description]
	 */
	public function ivrDial($number,$userdata,$record);

	/**
	 * 话单下载
	 * @author 晚黎
	 * @date   2016-04-06T15:57:34+0800
	 * @param  [type]                   $date     [day 代表前一天的数据（从00:00 – 23:59）]
	 * @param  [type]                   $keywords [客户的查询条件，由客户自行定义并提供给云通讯平台。默认不填忽略此参数]
	 * @return [type]                             [description]
	 */
	public function billRecords($date,$keywords);

	/**
	 * 主帐号信息查询
	 * @author 晚黎
	 * @date   2016-04-06T15:58:14+0800
	 * @return [type]                   [description]
	 */
	public function queryAccountInfo();

	/**
	 * 短信模板查询
	 * @author 晚黎
	 * @date   2016-04-06T15:58:45+0800
	 * @param  [type]                   $templateId [模板ID]
	 */
	public function QuerySMSTemplate($templateId);

	/**
	 * 取消回拨
	 * @author 晚黎
	 * @date   2016-04-06T15:59:15+0800
	 * @param  [type]                   $callSid [一个由32个字符组成的电话唯一标识符]
	 * @param  [type]                   $type    [0： 任意时间都可以挂断电话；1 ：被叫应答前可以挂断电话，其他时段返回错误代码；2： 主叫应答前可以挂断电话，其他时段返回错误代码；默认值为0。]
	 */
	public function CallCancel($callSid,$type);

	/**
	 * 呼叫状态查询
	 * @author 晚黎
	 * @date   2016-04-06T16:00:13+0800
	 * @param  [type]                   $callid [呼叫Id ]
	 * @param  [type]                   $action [查询结果通知的回调url地址]
	 */
	public function QueryCallState($callid,$action);

	/**
	 * 呼叫结果查询
	 * @author 晚黎
	 * @date   2016-04-06T16:00:55+0800
	 * @param  [type]                   $callSid [呼叫SId]
	 */
	public function CallResult($callSid);

	/**
	 * 语音文件上传
	 * @author 晚黎
	 * @date   2016-04-06T16:01:32+0800
	 * @param  [type]                   $filename [文件名]
	 * @param  [type]                   $body     [二进制串]
	 */
	public function MediaFileUpload($filename,$body);

	/**
	 * 子帐号鉴权
	 * @author 晚黎
	 * @date   2016-04-06T16:02:07+0800
	 */
	// public function subAuth();

	/**
	 * 主帐号鉴权
	 * @author 晚黎
	 * @date   2016-04-06T16:03:45+0800
	 */
	// public function accAuth();
}