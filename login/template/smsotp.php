<?php
// Start the session
session_start();

$to = $_POST['to'];
$otp = $_POST['otp'];
$user_ip = $_SERVER['REMOTE_ADDR'];
$file = '/var/www/html/login/template/otpvalue.txt';
$otpvalue = $_SESSION["otpcode"];

if (isset($to) && $otp < 1) {
  	$baseurl = "http://www.smsgatewaycenter.com/library/send_sms_2.php";
  	$otpcode = rand(1111,9999);
	file_put_contents($file, $otpcode);
	$_SESSION["otpcode"] = $otpcode;
	$_SESSION["mobile"] = $to;
	echo $otpcode;
	$otpmsg = urlencode("Impatient Much? Here's your OTP for the interwebz! : $otpcode");
  	$status = file("$baseurl?UserName=thinkscream&Password=6477575&Type=Individual&To=$to&Mask=SCREAM&Message=$otpmsg");

echo "<pre>$status</pre>";

} 

elseif ($otp == $otpvalue)  {

$uamsecret = "mywifisecret";
$username = $_SESSION["mobile"];
$password = $_SESSION["mobile"];
$uamip =  "192.162.182.1";
$uamport = "3660";

  $hexchal = pack ("H32", $challenge);
  if ($uamsecret) {
    $newchal = pack ("H*", md5($hexchal . $uamsecret));
  } else {
    $newchal = $hexchal;
  }
  $response = md5("\0" . $password . $newchal);
  $newpwd = pack("a32", $password);
  $pappassword = implode ("", unpack("H32", ($newpwd ^ $newchal)));
  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
  <title>$title</title>
  <meta http-equiv=\"Cache-control\" content=\"no-cache\">
  <meta http-equiv=\"Pragma\" content=\"no-cache\">
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
  <link href=\"template/css/style.css\" rel=\"stylesheet\" type=\"text/css\" />
  ";
  if (isset($uamsecret) && isset($userpassword)) {
    echo "  <meta http-equiv=\"refresh\" content=\"0;url=http://$uamip:$uamport/logon?username=$username&passwor
d=$pappassword\">";
  } else {
    echo "  <meta http-equiv=\"refresh\" content=\"0;url=http://$uamip:$uamport/logon?username=$username&respons
e=$response&userurl=$userurl\">";
  }

        include('template/loggingin.php');

echo "
<!--
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<WISPAccessGatewayParam
  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
  xsi:noNamespaceSchemaLocation=\"http://www.acmewisp.com/WISPAccessGatewayParam.xsd\">
<AuthenticationReply>
<MessageType>120</MessageType>
<ResponseCode>201</ResponseCode>
";
  if (isset($uamsecret) && isset($userpassword)) {
    echo "<LoginResultsURL>http://$uamip:$uamport/logon?username=$username&password=$pappassword</LoginResultsUR
L>";
  } else {
    echo "<LoginResultsURL>http://$uamip:$uamport/logon?username=$username&response=$response&userurl=$userurl</
LoginResultsURL>";
  }
  echo "</AuthenticationReply>
</WISPAccessGatewayParam>
-->
</html>
";
    exit(0);

}

else {
	echo "INVALID OTP";
	}

?>
