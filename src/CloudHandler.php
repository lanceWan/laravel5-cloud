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
        } else {
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
	/**
	 * 创建子账号
	 * @author 晚黎
	 * @date   2016-04-08T14:44:07+0800
	 * @param  [type]                   $friendlyName [description]
	 * @return [type]                                 [description]
	 */
	public function createSubAccount($friendlyName)
	{
		// 调用云通讯平台的创建子帐号,绑定您的子帐号名称
	    $result = $this->cloud->createSubAccount($friendlyName);
	    if($result == NULL ) {
	        return ['resultStatus' => false,'errorMsg' => 'result error!'];
	    }
	    if($result->statusCode!=0) {
	        return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
	    }else {
	        // 获取返回信息
	        $subaccount = $result->SubAccount;
	        return [
           		'resultStatus' => true , 
           		'subAccountid' => $subaccount->subAccountSid , 
           		'subToken' => $subaccount->subToken,
           		'dateCreated' => $subaccount->dateCreated,
           		'voipAccount' => $subaccount->voipAccount,
           		'voipPwd' => $subaccount->voipPwd
           	];
	    }   
	}

	/**
	 * 获取子账号信息
	 * @author 晚黎
	 * @date   2016-04-08T14:56:28+0800
	 * @param  [type]                   $startNo [description]
	 * @param  [type]                   $offset  [description]
	 * @return [type]                            [description]
	 */
	public function getSubAccounts($startNo,$offset)
	{
		// 调用云通讯平台的获取子帐号接口
	    $result = $this->cloud->getSubAccounts($startNo,$offset);
	    if($result == NULL ) {
	        return ['resultStatus' => false,'errorMsg' => 'result error!'];
	    }
	    if($result->statusCode!=0) {
	        return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
	    }else {
	        // 获取返回信息
	        $subaccount = $result->SubAccount;
	        return [
           		'resultStatus' => true ,
           		'subaccount' => $subaccount
           	];
	    }         
	}
	/**
	 * 通过子账号名称查询信息
	 * @author 晚黎
	 * @date   2016-04-08T15:04:17+0800
	 * @param  string                   $value [description]
	 * @return [type]                          [description]
	 */
	public function querySubAccount($friendlyName)
	{
		// 调用云通讯平台的子帐号信息查询接口
	    $result = $this->cloud->querySubAccount($friendlyName);
	    if($result == NULL ) {
	        return ['resultStatus' => false,'errorMsg' => 'result error!'];
	    }
	    
	    if($result->statusCode!=0) {
	        return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
	    }else {
	        // 获取返回信息
	        $subaccount = $result->SubAccount;
	        return [
           		'resultStatus' => true , 
           		'subAccountid' => $subaccount->subAccountSid , 
           		'subToken' => $subaccount->subToken,
           		'dateCreated' => $subaccount->dateCreated,
           		'voipAccount' => $subaccount->voipAccount,
           		'voipPwd' => $subaccount->voipPwd
           	];
	    }      
	}

	/**
	 * 发送模板短信
	 * @author 晚黎
	 * @date   2016-04-08T15:11:13+0800
	 * @param  [type]                   $to     [description]
	 * @param  [type]                   $datas  [description]
	 * @param  [type]                   $tempId [description]
	 * @return [type]                           [description]
	 */
	public function sendTemplateSMS($to,$datas,$tempId)
	{
		// 发送模板短信
	    $result = $this->cloud->sendTemplateSMS($to,$datas,$tempId);
	    if($result == NULL ) {
	        return ['resultStatus' => false,'errorMsg' => 'result error!'];
	    }
	    if($result->statusCode!=0) {
	        return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
	    }else{
	        // 获取返回信息
	        $smsmessage = $result->TemplateSMS;
	        return [
           		'resultStatus' => true , 
           		'dateCreated' => $smsmessage->dateCreated , 
           		'smsMessageSid' => $smsmessage->smsMessageSid
           	];
	    }
	}

	/**
	 * 外呼通知，暂时不开发
	 * @author 晚黎
	 * @date   2016-04-08T15:15:40+0800
	 * @return [type]                   [description]
	 */
	public function landingCall()
	{
		
	}

	/**
	 * 语音验证码
	 * @author 晚黎
	 * @date   2016-04-08T15:25:04+0800
	 * @param  [type]                   $verifyCode [description]
	 * @param  [type]                   $to         [description]
	 * @param  [type]                   $playTimes  [description]
	 * @param  [type]                   $displayNum [description]
	 * @param  [type]                   $respUrl    [description]
	 * @param  [type]                   $lang       [description]
	 * @param  [type]                   $userData   [description]
	 * @return [type]                               [description]
	 */
	public function voiceVerify($verifyCode,$to,$playTimes,$displayNum,$respUrl,$lang,$userData)
	{
		//调用语音验证码接口
        $result = $this->cloud->voiceVerify($verifyCode,$to,$playTimes,$displayNum,$respUrl,$lang,$userData);
         if($result == NULL ) {
           return ['resultStatus' => false,'errorMsg' => 'result error!'];
        }

        if($result->statusCode!=0) {
            return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
        } else{
            // 获取返回信息
            $voiceVerify = $result->VoiceVerify;
            return [
           		'resultStatus' => true , 
           		'callSid' => $voiceVerify->callSid , 
           		'dateCreated' => $voiceVerify->dateCreated
           	];
        }
	}

	/**
	 * IVR外呼
	 * @author 晚黎
	 * @date   2016-04-08T15:26:39+0800
	 * @param  [type]                   $number   [description]
	 * @param  [type]                   $userdata [description]
	 * @param  [type]                   $record   [description]
	 * @return [type]                             [description]
	 */
	public function ivrDial($number,$userdata,$record)
	{
		// 调用IVR外呼接口
     	$result = $this->cloud->ivrDial($number,$userdata,$record);
		if($result == NULL ) {
			return ['resultStatus' => false,'errorMsg' => 'result error!'];
		}
		if($result->statusCode!=0) {
			return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
		}else{
			//获取返回信息
			return [
           		'resultStatus' => true , 
           		'callSid' => $result->callSid
           	];
		}     
	}

	/**
	 * 话单下载
	 * @author 晚黎
	 * @date   2016-04-08T15:37:42+0800
	 * @param  string                   $date     [description]
	 * @param  string                   $keywords [description]
	 * @return [type]                             [description]
	 */
	public function billRecords($date = 'day',$keywords = '')
	{
		// 调用话单下载接口
	    $result = $this->cloud->billRecords($date,$keywords);
	    if($result == NULL ) {
			return ['resultStatus' => false,'errorMsg' => 'result error!'];
		}
		if($result->statusCode!=0) {
			return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
	    }else{
	       // 获取返回信息
	       return [
           		'resultStatus' => true , 
           		'downUrl' => $result->downUrl,
           		'token' => $result->token
           	];
	    }     
	}

	/**
	 * 短信模板查询
	 * @author 晚黎
	 * @date   2016-04-08T15:49:34+0800
	 * @param  [type]                   $templateId [description]
	 * @return [type]                               [description]
	 */
	public function querySMSTemplate($templateId)
	{
		// 调用短信模板查询接口
    	$result = $this->cloud->querySMSTemplate($templateId);
    	if($result == NULL ) {
			return ['resultStatus' => false,'errorMsg' => 'result error!'];
		}
		if($result->statusCode!=0) {
			return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
    	}else{              
    	   	$templateSMS = $result->TemplateSMS;
    	   	return [
           		'resultStatus' => true , 
           		'templateSMS' => $result->templateSMS
           	];
    	} 
	}

	/**
	 * 取消回拨
	 * @author 晚黎
	 * @date   2016-04-08T15:52:59+0800
	 * @param  [type]                   $callSid [description]
	 * @param  [type]                   $type    [description]
	 * @return [type]                            [description]
	 */
	public function callCancel($callSid,$type)
	{
		// 调用取消回拨接口
	    $result = $this->cloud->callCancel($callSid,$type);
	    if($result == NULL ) {
			return ['resultStatus' => false,'errorMsg' => 'result error!'];
		}
		if($result->statusCode!=0) {
			return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
	    }else{
	       	return [
           		'resultStatus' => true , 
           		'statusCode' => $result->statusCode
           	];
	    }     
	}
	/**
	 * 呼叫状态查询
	 * @author 晚黎
	 * @date   2016-04-08T15:55:53+0800
	 * @param  [type]                   $callid [description]
	 * @param  [type]                   $action [description]
	 * @return [type]                           [description]
	 */
	public function queryCallState($callid,$action)
	{
		// 调用呼叫状态查询接口
	    $result = $this->cloud->queryCallState($callid,$action);
	    if($result == NULL ) {
				return ['resultStatus' => false,'errorMsg' => 'result error!'];
		}
		if($result->statusCode!=0) {
			return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
	    }else{
	       	return [
           		'resultStatus' => true , 
           		'state' => $result->state,
           		'callSid' => $result->callSid
           	];
	    }
	}

	/**
	 * 呼叫结果查询
	 * @author 晚黎
	 * @date   2016-04-08T15:58:58+0800
	 * @param  [type]                   $callSid [description]
	 * @return [type]                            [description]
	 */
	public function callResult($callSid)
	{
		// 调用呼叫结果查询接口
    	$result = $this->cloud->callResult($callSid);
    	if($result == NULL ) {
				return ['resultStatus' => false,'errorMsg' => 'result error!'];
		}
		if($result->statusCode!=0) {
			return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
    	}else{
    	    return [
           		'resultStatus' => true , 
           		'friendlyName' => $callResult->callTime,
           		'type' => $callResult->state
           	];
    	}
	}

	/**
	 * 语音文件上传
	 * @author 晚黎
	 * @date   2016-04-08T16:02:27+0800
	 * @param  [type]                   $filename [description]
	 * @param  [type]                   $body     [description]
	 * @return [type]                             [description]
	 */
	public function mediaFileUpload($filename,$body)
	{
		$filePath = $path;                         
	    $fh = fopen($filePath, "rb");
	    $body = fread($fh, filesize($filePath));
	    fclose($fh);
	    
	    // 调用语音文件上传接口
	    $result = $this->cloud->mediaFileUpload($filename,$body);
	    if($result == NULL ) {
				return ['resultStatus' => false,'errorMsg' => 'result error!'];
		}
		if($result->statusCode!=0) {
			return ['resultStatus' => false , 'errorCode' => $result->statusCode , 'errorMsg' => $result->statusMsg];
	    }else{
	       	return [
           		'resultStatus' => true
           	];
	    }
	}
}