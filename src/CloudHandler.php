<?php
namespace Lance\Cloud;
use Lance\Cloud\CloudCommunicationContract;
use Illuminate\Config\Repository;
/**
* 云通讯实现方法类
*/
class CloudHandler
{
	protected $cloud;

	function __construct(CloudCommunicationContract $cloud)
	{
		$this->cloud = $cloud;
	}

	/**
	 * 双向回呼
	 * $options参数为下面变量
	 * @author 晚黎
	 * @date   2016-04-06T17:25:44+0800
	 * @param                     from            [主叫电话号码]
	 * @param                     to              [被叫电话号码]
	 * @param                     customerSerNum  [被叫侧显示的客服号码]
	 * @param                     fromSerNum      [主叫侧显示的号码]
	 * @param                     promptTone      [自定义回拨提示音]
	 * @param                     alwaysPlay      [第三方私有数据]
	 * @param                     terminalDtmf    [最大通话时长]
	 * @param                     userData        [实时话单通知地址]
	 * @param                     maxCallTime     [是否一直播放提示音]
	 * @param                     hangupCdrUrl    [用于终止播放promptTone参数定义的提示音]
	 * @param                     needBothCdr     [是否给主被叫发送话单]
	 * @param                     needRecord      [是否录音]
	 * @param                     countDownTime   [设置倒计时时间]
	 * @param                     countDownPrompt [倒计时时间到后播放的提示音]
	 * @return                                     [description]
	 */
	public function callBack($from,$to,$options = [])
	{
		// 调用回拨接口
        $result = $this->cloud->callBack($from,$to,$options);
        if($result == NULL ) {
            return ['resultStatus' => false,'errorMsg' => 'result error!'];
        }
        if($result->statusCode!=0) {
            return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
        } else {
           $callback = $result->CallBack;
           return ['resultStatus' => true , 'callSid' => $callback->dateCreated , 'dateCreated' => $callback->dateCreated];
      	}    
	}

	/**
	 * 主帐号信息查询
	 * @author 晚黎
	 * @date   2016-04-07T12:01:22+0800
	 * @return [type]                   [description]
	 */
	public function queryAccountInfo()
	{
		//调用主帐号信息查询接口
		$result = $this->cloud->queryAccountInfo();
		if($result == NULL ) {
            return ['resultStatus' => false,'errorMsg' => 'result error!'];
        }
        if($result->statusCode!=0) {
            return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
        } else {resultStatus
        	$account = $result->Account;
           	return [
           		'resultStatus' => true , 
           		'friendlyName' => $account->friendlyName , 
           		'type' => $account->type,
           		'status' => $account->status,
           		'dateCreated' => $account->dateCreated,
           		'dateUpdated' => $account->dateUpdated,
           		'balance' => $account->balance,
           	];
      	} 
	}
}