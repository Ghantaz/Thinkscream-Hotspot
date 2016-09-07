<?php

#Author : Gorbachev Anthony
#email  : anthony.dexotr@gmail.com
#mobile : 9820926605 

// Start the session

function debug_to_console( $data ) {

    if
     ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}

session_start();

include('include/db.conf');

if(isset($_POST['challenge']))
	$challenge = $_POST['challenge'];
if(isset($_POST['pass']))
	$pass = $_POST['pass'];
//$otp = $_POST['otp'];
//$user_ip = $_SERVER['REMOTE_ADDR'];
$file = "/home/ubuntu/pass.txt";
//$passvalue = $_SESSION["otpcode"];
//$passvalue = "helloo";
//if (isset($to) && $otp < 1) {
$passvalue = file_get_contents($file);
$_SESSION["passvalue"] = $passvalue;
	//$_SESSION["mobile"] = $to;
$_SESSION["challenge"] = $challenge;

$passvalue = strval($passvalue);
$pass = strval($pass);
//echo "<h2>$passvalue and $pass</h2>";
	//$otpmsg = urlencode("Impatient Much? Here's your OTP for the interwebz! : $otpcode");
  	//$status = file("$otp_url?UserName=thinkscream&Password=6477575&Type=Individual&To=$to&Mask=SCREAM&Message=$otpmsg");

//debug_to_console($pass)
if (strncmp($pass,$passvalue, 5) === 0)  {
//echo $_SESSION["passvalue"];
//echo $_SESSION["challenge"];
//echo "</h2>";



// Create connection
$conn = mysqli_connect("localhost", "root", "raspbian", "radius");
// Check connection

if ($conn->connect_errno) {
    echo " hello      $conn->connect_error </h2>";
    //die("Connection failed: " . $conn->connect_error);
}



$sql = "DELETE from radcheck WHERE username = '$pass'";
$conn->query($sql);

$sql = "INSERT INTO radcheck (username, attribute, op, value) VALUES ('$pass', 'Cleartext-Password', ':=', '$pass')";
$sql2 = "INSERT INTO radcheck (username, attribute, op, value) VALUES ('$pass', 'Simultaneous-Use', ':=', '1')";
//$sql3 = "INSERT INTO radcheck (username, attribute, op, value) VALUES ('$pass', 'Max-All-Session', ':=', '10300')";


if ($conn->query($sql) === TRUE)
{
    echo "<h2>User added to database</h2>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->query($sql2); 
//$conn->query($sql3); 
$conn->close();

$uamsecret = "change-me";
$username = $pass;
$password = $pass;
//$challenge = $_SESSION["challenge"];
$uamip =  $_SESSION['uamip'];
$uamip = "192.168.1.1";

echo $challenge;

$uamport = "3990";

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
  if (isset($uamsecret) && isset($pappassword)) {
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
	echo "<h2>INVALID OTP, please try again.</h2>";
	}

?>
