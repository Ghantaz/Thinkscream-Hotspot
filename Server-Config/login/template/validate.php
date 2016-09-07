<?php 
$data = $_REQUEST;
$config = file_get_contents("config.json");
$config = json_decode($config,true);
function fetchContentWithPost($url,$array)
{
		if(empty($url))
		{
				return;
		}
		/*$queryString = "";
		foreach($array as $key=>$value)
		{
				$queryString.= $key."=".$value."&";
		}*/
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
//	        curl_setopt($ch, CURLOPT_POST, false);
//	        curl_setopt($ch, CURLOPT_POSTFIELDS,$array);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
		$headers = array();
		$headers[]='Content-type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		$headersize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$responsestatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$header = substr($response,0,$headersize);
		$body = substr($response,$headersize);
		$inputreturn  = array($header,$body,$responsestatus);
		return $inputreturn;
}
$data['success'] = 1;
if(isset($data['Phone'])&& empty($data['OTP'])&&empty($data['gotp'])){
	$num = str_pad(mt_rand(1,9999),4,'0',STR_PAD_LEFT);
	//echo $num;
	$sms_url = $config["sms_url"].'?method='.urlencode('SendMessage').'&msg_type='.urlencode('TEXT').'&userid='.urlencode($config['sms_username']).'&auth_scheme='.urlencode('plain').'&send_to=91'.urlencode($data['Phone']).'&msg='.urlencode('Your OTP is '.$num.'. Please enter this to verify your identity and proceed. Do not share this OTP with anyone.').'&password='.urlencode($config['sms_password']).'&v=1.1&format=json&overide_dnd=true';
	//$res = fetchContentWithPost($sms_url,array());
	//print_r($res);
	//echo $sms_url;
	$data['sms_response'] = $res;
	$data['gotp'] = $num;
}else if(!empty($data['OTP'])&&!empty($data['gotp'])){
	if($data['OTP']==$data['gotp']){
		$data['msg']="Success";
		$adduserUrl = $config['addUserUrl'];
		$adduserUrl = str_replace("{user}",$data['Phone'],$adduserUrl);
		$adduserUrl = str_replace("{pass}",$data['Phone'],$adduserUrl);
		//echo $adduserUrl;
		$res = fetchContentWithPost($adduserUrl,array());
		//print_r($res);
		if($res[2]==200){
			$data['adderror']="";
		}else{
			$data['adderror']="Error";
		}
	}else{
		$data['error'] = "OTP Not Match.";
		$data['success']=0;
	}
}else{
	$data['error'] = "Please Provide Valid Data.";
	$data['success']=0;
}

echo json_encode($data);
?>