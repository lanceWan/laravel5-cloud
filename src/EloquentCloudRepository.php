<?php
namespace Lance\Cloud;
use Lance\Cloud\CloudCommunicationContract;
use Illuminate\Config\Repository;
/**
* 云通讯实现类
*/
class EloquentCloudRepository implements CloudCommunicationContract
{

	// AccountSid 主帐号
	private $accountSid;
	// 主帐号Token
	private $accountToken;
	// appId 应用ID
	private $appId;
	// aubAccountSid 子帐号
	private $subAccountSid;
	// subAccountToken 子帐号Token(密码)
	private $subAccountToken;
	// soIPAccount VoIP帐号
	private $voIPAccount;
	// voIPPassword VoIP密码
	private $voIPPassword; 
	//请求地址，格式如下，不需要写https://
	private $serverIP;
	//请求端口
	private $serverPort;
	//REST版本号
	private $softVersion;
	//时间sh
	private $batch;
	//包体格式，可填值：json 、xml
	private $bodyType;
	//日志开关。可填值：true、
	private $enabeLog;
	//日志文件
	private $filename="../log.txt";

	private $handle;

	protected $config;

	function __construct(Repository $config)
	{
		$this->config = $config;
		$this->initConfiguration();
	}

	/**
	 * 初始化参数
	 * @author 晚黎
	 * @date   2016-04-06T17:23:47+0800
	 */
	public function initConfiguration()
	{
		$this->accountSid 		= $this->config->get('cloud.accountSid');
		$this->accountToken 	= $this->config->get('cloud.accountToken');
		$this->appId 			= $this->config->get('cloud.appId');
		$this->subAccountSid 	= $this->config->get('cloud.subAccountSid');
		$this->subAccountToken 	= $this->config->get('cloud.subAccountToken');
		$this->voIPAccount 		= $this->config->get('cloud.voIPAccount');
		$this->voIPPassword 	= $this->config->get('cloud.voIPPassword');
		$this->serverIP 		= $this->config->get('cloud.serverIP');
    $this->serverPort     = $this->config->get('cloud.serverPort');
		$this->softVersion 		= $this->config->get('cloud.softVersion');
		$this->bodyType 		= $this->config->get('cloud.bodyType');
		$this->enabeLog 		= $this->config->get('cloud.enabeLog');
		$this->batch 			= date("YmdHis");
	}

	/**
	 * 打印日志
	 * @author 晚黎
	 * @date   2016-04-06T15:45:01+0800
	 * @param  [type]                   $log [日志内容]
	 * @return [type]                        [description]
	 */
	public function showlog($log)
	{

	}
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
	public function curl_post($url,$data,$header,$post=1)
	{
		//初始化curl
       	$ch = curl_init();
       	//参数设置  
       	$res= curl_setopt ($ch, CURLOPT_URL,$url);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
       curl_setopt ($ch, CURLOPT_HEADER, 0);
       curl_setopt($ch, CURLOPT_POST, $post);
       if($post)
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
       curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
       $result = curl_exec ($ch);
       	//连接失败
       	if($result == FALSE){
          	if($this->bodyType=='json'){
             	$result = "{\"statusCode\":\"172001\",\"statusMsg\":\"网络错误\"}";
          	} else {
             	$result = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><Response><statusCode>172001</statusCode><statusMsg>网络错误</statusMsg></Response>"; 
          	}    
       	}
       curl_close($ch);
       return $result;
	}
	/**
	 * 创建子帐号
	 * @author 晚黎
	 * @date   2016-04-06T15:47:12+0800
	 * @param  [type]                   $friendlyName [子帐号名称]
	 * @return [type]                                 [description]
	 */
	public function createSubAccount($friendlyName)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->bodyType=="json"){
           $body= "{'appId':'$this->appId','friendlyName':'$friendlyName'}";
        }else{
           $body="<SubAccount>
                    <appId>$this->appId</appId>
                    <friendlyName>$friendlyName</friendlyName>
                  </SubAccount>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数  
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/Accounts/$this->accountSid/SubAccounts?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐号Id + 英文冒号 + 时间戳
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头 
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->bodyType=="json"){//JSON格式
           $datas=json_decode($result); 
        }else{ //xml格式
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
	}

	/**
	 * 获取子帐号
	 * @author 晚黎
	 * @date   2016-04-06T15:48:30+0800
	 * @param  [type]                   $startNo [开始的序号，默认从0开始]
	 * @param  [type]                   $offset  [一次查询的最大条数，最小是1条，最大是100条]
	 * @return [type]                            [description]
	 */
	public function getSubAccounts($startNo,$offset)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->bodyType=="json"){
           $body= "{'appId':'$this->appId','startNo':'$startNo','offset':'$offset'}";
        }else{
        	 $body="
            <SubAccount>
              <appId>$this->appId</appId>
              <startNo>$startNo</startNo>  
              <offset>$offset</offset>
            </SubAccount>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数  
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/Accounts/$this->accountSid/GetSubAccounts?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头 
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->bodyType=="json"){//JSON格式
           $datas=json_decode($result); 
        }else{ //xml格式
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
	}

	/**
	 * 子帐号信息查询
	 * @author 晚黎
	 * @date   2016-04-06T15:50:11+0800
	 * @param  [type]                   $friendlyName [子帐号名称]
	 * @return [type]                                 [description]
	 */
	public function querySubAccount($friendlyName)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->bodyType=="json"){
           $body= "{'appId':'$this->appId','friendlyName':'$friendlyName'}";
        }else{
        	 $body="
            <SubAccount>
              <appId>$this->appId</appId>
              <friendlyName>$friendlyName</friendlyName>
            </SubAccount>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数  
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/Accounts/$this->accountSid/QuerySubAccountByName?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头 
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->bodyType=="json"){//JSON格式
           $datas=json_decode($result); 
        }else{ //xml格式
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
	}

	/**
	 * 发送模板短信
	 * @author 晚黎
	 * @date   2016-04-06T15:50:58+0800
	 * @param  [type]                   $to     [短信接收彿手机号码集合,用英文逗号分开]
	 * @param  [type]                   $datas  [内容数据]
	 * @param  [type]                   $tempId [模板Id]
	 * @return [type]                           [description]
	 */
	public function sendTemplateSMS($to,$datas,$tempId)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->bodyType=="json"){
           $data="";
           for($i=0;$i<count($datas);$i++){
              $data = $data. "'".$datas[$i]."',"; 
           }
           $body= "{'to':'$to','templateId':'$tempId','appId':'$this->appId','datas':[".$data."]}";
        }else{
           $data="";
           for($i=0;$i<count($datas);$i++){
              $data = $data. "<data>".$datas[$i]."</data>"; 
           }
           $body="<TemplateSMS>
                    <to>$to</to> 
                    <appId>$this->appId</appId>
                    <templateId>$tempId</templateId>
                    <datas>".$data."</datas>
                  </TemplateSMS>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数 
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL        
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/Accounts/$this->accountSid/SMS/TemplateSMS?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头  
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->bodyType=="json"){//JSON格式
           $datas=json_decode($result); 
        }else{ //xml格式
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        //重新装填数据
        if($datas->statusCode==0){
         if($this->bodyType=="json"){
            $datas->TemplateSMS =$datas->templateSMS;
            unset($datas->templateSMS);   
          }
        }
        return $datas;
	}

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
	public function callBack($from,$to,$options=[])
	{
		//子帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->subAuth();
        if($auth!=""){
            return $auth;
        }

        //判断是否有额外参数
        if ($options) {
        	$data = [
        		'customerSerNum'	=> isset($options['customerSerNum']) ? $options['customerSerNum']:'',
				'fromSerNum' 		=> isset($options['fromSerNum']) ? $options['fromSerNum']:'',
				'promptTone' 		=> isset($options['promptTone']) ? $options['promptTone']:'',
				'alwaysPlay' 		=> isset($options['alwaysPlay']) ? $options['alwaysPlay']:'',
				'terminalDtmf' 		=> isset($options['terminalDtmf']) ? $options['terminalDtmf']:'',
				'userData' 			=> isset($options['userData']) ? $options['userData']:'',
				'maxCallTime' 		=> isset($options['maxCallTime']) ? $options['maxCallTime']:'',
				'hangupCdrUrl' 		=> isset($options['hangupCdrUrl']) ? $options['hangupCdrUrl']:'',
				'needBothCdr' 		=> isset($options['needBothCdr']) ? $options['needBothCdr']:'',
				'needRecord' 		=> isset($options['needRecord']) ? $options['needRecord']:'',
				'countDownTime'		=> isset($options['countDownTime']) ? $options['countDownTime']:'',
				'countDownPrompt' 	=> isset($options['countDownPrompt']) ? $options['countDownPrompt']:''
        	];
        }else{
        	$data = $this->config->get('cloud.callBack');
        }

        // 拼接请求包体 
        if($this->bodyType=="json"){
        	$body = json_encode([
        			'from' 				=> $from,
        			'to' 				=> $to,
        			'customerSerNum'	=> $data['customerSerNum'],
					'fromSerNum' 		=> $data['fromSerNum'],
					'promptTone' 		=> $data['promptTone'],
					'alwaysPlay' 		=> $data['alwaysPlay'],
					'terminalDtmf' 		=> $data['terminalDtmf'],
					'userData' 			=> $data['userData'],
					'maxCallTime' 		=> $data['maxCallTime'],
					'hangupCdrUrl' 		=> $data['hangupCdrUrl'],
					'needBothCdr' 		=> $data['needBothCdr'],
					'needRecord' 		=> $data['needRecord'],
					'countDownTime'		=> $data['countDownTime'],
					'countDownPrompt' 	=> $data['countDownPrompt'],
        		]);
        }else{
           $body= "<CallBack>
                     <from>$from</from>
                     <to>$to</to>
                     <customerSerNum>".$data['customerSerNum']."</customerSerNum>
                     <fromSerNum>".$data['fromSerNum']."</fromSerNum>
                     <promptTone>".$data['promptTone']."</promptTone>
		             <userData>".$data['userData']."</userData>
		             <maxCallTime>".$data['maxCallTime']."</maxCallTime>
		             <hangupCdrUrl>".$data['hangupCdrUrl']."</hangupCdrUrl>
                     <alwaysPlay>".$data['alwaysPlay']."</alwaysPlay>
                     <terminalDtmf>".$data['terminalDtmf']."</terminalDtmf>
                     <needBothCdr>".$data['needBothCdr']."</needBothCdr>
                     <needRecord>".$data['needRecord']."</needRecord>
                     <countDownTime>".$data['countDownTime']."</countDownTime>
                     <countDownPrompt>".$data['countDownPrompt']."</countDownPrompt>
                   </CallBack>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数  
        $sig =  strtoupper(md5($this->subAccountSid . $this->subAccountToken . $this->batch));
        // 生成请求URL
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/SubAccounts/$this->subAccountSid/Calls/Callback?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：子帐号Id + 英文冒号 + 时间戳 
        $authen=base64_encode($this->subAccountSid . ":" . $this->batch);
        // 生成包头 
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发请求
        $result = $this->curl_post($url,$body,$header);

        $this->showlog("response body = ".$result);

        if($this->bodyType=="json"){//JSON格式
           	$datas=json_decode($result);
        }else{ //xml格式
           	$datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
	}

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
	public function landingCall($to,$mediaName,$mediaTxt,$displayNum,$playTimes,$respUrl,$userData,$maxCallTime,$speed,$volume,$pitch,$bgsound)
	{

	}

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
	public function voiceVerify($verifyCode,$playTimes,$to,$displayNum,$respUrl,$lang,$userData)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->bodyType=="json"){
           $body= "{'appId':'$this->appId','verifyCode':'$verifyCode','playTimes':'$playTimes','to':'$to','respUrl':'$respUrl','displayNum':'$displayNum',
           'lang':'$lang','userData':'$userData'}";
        }else{
           $body="<VoiceVerify>
                    <appId>$this->appId</appId>
                    <verifyCode>$verifyCode</verifyCode>
                    <playTimes>$playTimes</playTimes>
                    <to>$to</to>
                    <respUrl>$respUrl</respUrl>
                    <displayNum>$displayNum</displayNum>
                    <lang>$lang</lang>
                    <userData>$userData</userData>
                  </VoiceVerify>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL  
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/Accounts/$this->accountSid/Calls/VoiceVerify?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头  
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->bodyType=="json"){//JSON格式
           $datas=json_decode($result); 
        }else{ //xml格式
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
	}

	/**
	 * IVR外呼
	 * @author 晚黎
	 * @date   2016-04-06T15:56:20+0800
	 * @param  [type]                   $number   [待呼叫号码，为Dial节点的属性]
	 * @param  [type]                   $userdata [用户数据，在<startservice>通知中返回，只允许填写数字字符，为Dial节点的属性]
	 * @param  [type]                   $record   [是否录音，可填项为true和false，默认值为false不录音，为Dial节点的属性]
	 * @return [type]                             [description]
	 */
	public function ivrDial($number,$userdata,$record)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        } 
       // 拼接请求包体
        $body=" <Request>
                  <Appid>$this->appId</Appid>
                  <Dial number='$number'  userdata='$userdata' record='$record'></Dial>
                </Request>";
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL  
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/Accounts/$this->accountSid/ivr/dial?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头  
        $header = array("Accept:application/xml","Content-Type:application/xml;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        $datas = simplexml_load_string(trim($result," \t\n\r"));
        return $datas;
	}

	/**
	 * 话单下载
	 * @author 晚黎
	 * @date   2016-04-06T15:57:34+0800
	 * @param  [type]                   $date     [day 代表前一天的数据（从00:00 – 23:59）]
	 * @param  [type]                   $keywords [客户的查询条件，由客户自行定义并提供给云通讯平台。默认不填忽略此参数]
	 * @return [type]                             [description]
	 */
	public function billRecords($date,$keywords)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->bodyType=="json"){
           $body= "{'appId':'$this->appId','date':'$date','keywords':'$keywords'}";
        }else{
           $body="<BillRecords>
                    <appId>$this->appId</appId>
                    <date>$date</date>
                    <keywords>$keywords</keywords>
                  </BillRecords>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL  
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/Accounts/$this->accountSid/BillRecords?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头  
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->bodyType=="json"){//JSON格式
           $datas=json_decode($result); 
        }else{ //xml格式
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas; 
	}

	/**
	 * 主帐号信息查询
	 * @author 晚黎
	 * @date   2016-04-06T15:58:14+0800
	 * @return [type]                   [description]
	 */
	public function queryAccountInfo()
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }

        // 大写的sig参数
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL  
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/Accounts/$this->accountSid/AccountInfo?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头  
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,"",$header,0);

        $this->showlog("response body = ".$result);

        if($this->bodyType=="json"){//JSON格式
           	$datas=json_decode($result);
        }else{ //xml格式
           	$datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
	}

	/**
	 * 短信模板查询
	 * @author 晚黎
	 * @date   2016-04-06T15:58:45+0800
	 * @param  [type]                   $templateId [模板ID]
	 */
	public function QuerySMSTemplate($templateId)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->bodyType=="json"){
           $body= "{'appId':'$this->appId','templateId':'$templateId'}";
        }else{
           $body="<Request>
                    <appId>$this->appId</appId>
                    <templateId>$templateId</templateId>  
                  </Request>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL  
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/Accounts/$this->accountSid/SMS/QuerySMSTemplate?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头  
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header); 
        $this->showlog("response body = ".$result);
        if($this->bodyType=="json"){//JSON格式
           $datas=json_decode($result); 
        }else{ //xml格式 
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
	}

	/**
	 * 取消回拨
	 * @author 晚黎
	 * @date   2016-04-06T15:59:15+0800
	 * @param  [type]                   $callSid [一个由32个字符组成的电话唯一标识符]
	 * @param  [type]                   $type    [0： 任意时间都可以挂断电话；1 ：被叫应答前可以挂断电话，其他时段返回错误代码；2： 主叫应答前可以挂断电话，其他时段返回错误代码；默认值为0。]
	 */
	public function CallCancel($callSid,$type)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->subAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->bodyType=="json"){
           $body= "{'appId':'$this->appId','callSid':'$callSid','type':'$type'}";
        }else{
           $body="<CallCancel>
                    <appId>$this->appId</appId>
                    <callSid>$callSid</callSid>
                    <type>$type</type>
                  </CallCancel>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->subAccountSid . $this->subAccountToken . $this->batch));
        // 生成请求URL  
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/SubAccounts/$this->subAccountSid/Calls/CallCancel?sig=$sig";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->subAccountSid . ":" . $this->batch);
        // 生成包头  
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->bodyType=="json"){//JSON格式
           $datas=json_decode($result); 
        }else{ //xml格式
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
	}

	/**
	 * 呼叫状态查询
	 * @author 晚黎
	 * @date   2016-04-06T16:00:13+0800
	 * @param  [type]                   $callid [呼叫Id ]
	 * @param  [type]                   $action [查询结果通知的回调url地址]
	 */
	public function QueryCallState($callid,$action)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体
        if($this->bodyType=="json"){
           $body= "{'Appid':'$this->appId','QueryCallState':{'callid':'$callid','action':'$action'}}";
        }else{
           $body="<Request>
                    <Appid>$this->appId</Appid>
                    <QueryCallState callid ='$callid' action='$action'/>
                  </Request>";
        }
        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL  
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/Accounts/$this->accountSid/ivr/call?sig=$sig&callid=$callid";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头  
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->bodyType=="json"){//JSON格式
           $datas=json_decode($result); 
        }else{ //xml格式
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
	}

	/**
	 * 呼叫结果查询
	 * @author 晚黎
	 * @date   2016-04-06T16:00:55+0800
	 * @param  [type]                   $callSid [呼叫SId]
	 */
	public function CallResult($callSid)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 大写的sig参数
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL  
        $url="https://$this->serverIP:$this->serverPort/$this->softVersion/Accounts/$this->accountSid/CallResult?sig=$sig&callsid=$callSid";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头  
        $header = array("Accept:application/$this->bodyType","Content-Type:application/$this->bodyType;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,"",$header,0);
        $this->showlog("response body = ".$result);
        if($this->bodyType=="json"){//JSON格式
           $datas=json_decode($result); 
        }else{ //xml格式
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
	}

	/**
	 * 语音文件上传
	 * @author 晚黎
	 * @date   2016-04-06T16:01:32+0800
	 * @param  [type]                   $filename [文件名]
	 * @param  [type]                   $body     [二进制串]
	 */
	public function MediaFileUpload($filename,$body)
	{
		//主帐号鉴权信息验证，对必选参数进行判空。
        $auth=$this->accAuth();
        if($auth!=""){
            return $auth;
        }
        // 拼接请求包体

        $this->showlog("request body = ".$body);
        // 大写的sig参数
        $sig =  strtoupper(md5($this->accountSid . $this->accountToken . $this->batch));
        // 生成请求URL  
        $url="https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->accountSid/Calls/MediaFileUpload?sig=$sig&appid=$this->appId&filename=$filename";
        $this->showlog("request url = ".$url);
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->accountSid . ":" . $this->batch);
        // 生成包头  
        $header = array("Accept:application/$this->bodyType","Content-Type:application/octet-stream","Authorization:$authen");
        // 发送请求
        $result = $this->curl_post($url,$body,$header);
        $this->showlog("response body = ".$result);
        if($this->bodyType=="json"){//JSON格式
           $datas=json_decode($result);
        }else{ //xml格式
           $datas = simplexml_load_string(trim($result," \t\n\r"));
        }
        return $datas;
	}

	/**
	 * 子帐号鉴权
	 * @author 晚黎
	 * @date   2016-04-06T16:02:07+0800
	 */
	public function subAuth()
	{
		if($this->serverIP==""){
            $data = new \stdClass();
            $data->statusCode = '172004';
            $data->statusMsg = 'IP为空';
          	return $data;
        }
        if($this->serverPort<=0){
            $data = new \stdClass();
            $data->statusCode = '172005';
            $data->statusMsg = '端口错误（小于等于0）';
          	return $data;
        }
        if($this->softVersion==""){
            $data = new \stdClass();
            $data->statusCode = '172013';
            $data->statusMsg = '版本号为空';
          	return $data;
        } 
        if($this->subAccountSid==""){
            $data = new \stdClass();
            $data->statusCode = '172008';
            $data->statusMsg = '子帐号为空';
          	return $data;
        }
        if($this->subAccountToken==""){
            $data = new \stdClass();
            $data->statusCode = '172009';
            $data->statusMsg = '子帐号令牌为空';
          	return $data;
        }
        if($this->appId==""){
            $data = new \stdClass();
            $data->statusCode = '172012';
            $data->statusMsg = '应用ID为空';
          	return $data;
        }
	}

	/**
	 * 主帐号鉴权
	 * @author 晚黎
	 * @date   2016-04-06T16:03:45+0800
	 */
	public function accAuth()
	{
		if($this->serverIP==""){
            $data = new \stdClass();
            $data->statusCode = '172004';
            $data->statusMsg = 'IP为空';
          	return $data;
        }
        if($this->serverPort<=0){
            $data = new \stdClass();
            $data->statusCode = '172005';
            $data->statusMsg = '端口错误（小于等于0）';
          	return $data;
        }
        if($this->softVersion==""){
            $data = new \stdClass();
            $data->statusCode = '172013';
            $data->statusMsg = '版本号为空';
          	return $data;
        } 
        if($this->accountSid==""){
            $data = new \stdClass();
            $data->statusCode = '172006';
            $data->statusMsg = '主帐号为空';
          	return $data;
        }
        if($this->accountToken==""){
            $data = new \stdClass();
            $data->statusCode = '172007';
            $data->statusMsg = '主帐号令牌为空';
          	return $data;
        }
        if($this->appId==""){
            $data = new \stdClass();
            $data->statusCode = '172012';
            $data->statusMsg = '应用ID为空';
          	return $data;
        }
	}
}