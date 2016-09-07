<?php

//Author: Tanmaya Mishra
//Company: Thinkscream Infomedia Pvt. Ltd.

//This script is a collector of information for display on the admin page. 
//I use a custom JSON format used by scraping the output of chilli_query command
//The json is posted here by each router, and the logdata database is
//updated.

ini_set("date.timezone", "Asia/Kolkata");

$servername = "localhost";
$username = "root";
$password = "raspbian";
$dbname = "logdata";

echo "<!DOCTYPE html>
<html>
<body>

<h1> HIIIIIIIIIII </h1>";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error){
	echo "<h2> error while connecting </h2>";
}

$json = file_get_contents('php://input');
//echo "<h2> $json </h2>";
//echo file_put_contents("/home/ubuntu/posted_data.txt", $json);

echo "<h2>";
$obj = json_decode($json);
$routermac = $obj->RouterMAC;
$routerip = $obj->RouterIP;

$numberOfNodes = count($obj->NodeList)-1; //-1 because I have added a extra node as a hack to fix formatting problems of json

//Generate a timestamp compatible with mysql datetime format

$timestamp = date("Y-m-d H:i:s");
echo date_default_timezone_get();
echo "      $timestamp     ";

//first check if the Chilli status is online
$chilliStatus = $obj->ChilliStatus;
if ($chilliStatus == "online"){
	//chilli is online! Good boy chilli
	//so chilli is online and all data received is guaranteed to be good
	//first i'll clear up the entries in the table. 
	//And repopulate it with my new json 
	
	$query = "delete from log where RouterMAC='$routermac'";
	$result = $conn->query($query);

	//now lets repopulate. The Nodelist is an array.
	
	foreach ($obj->NodeList as $node){
		$mac = $node->MAC;
		if ($mac == ""){
			break;
		}
		$ip = $node->IP;
		$auth = $node->Auth;
		echo $auth;
		//check if auth is yes then continue gathering data

		if ($auth == "yes"){
			$username = $node->UserName;
			$sessiontime = $node->SessionTime;
			$maxsessiontime = $node->MaxSessionTime;
			$idletime = $node->IdleTime;
			$maxidletime = $node->MaxIdleTime;
			$totalaccounttime = $node->TotalAccountTime;
			$maxtotalaccounttime = $node->MaxTotalAccountTime;
			$query = "insert into log (Timestamp, RouterIP, RouterMAC, MAC, IP, Auth, UserName, SessionTime, MaxSessionTime, IdleTime, MaxIdleTime, TotalAccountTime, MaxTotalAccountTime, ChilliStatus) values ('$timestamp', '$routerip', '$routermac', '$mac', '$ip', '$auth', '$username', $sessiontime, $maxsessiontime, $idletime, $maxidletime, $totalaccounttime, $maxtotalaccounttime, 'online')";		
			$result = $conn->query($query);
			if (!$result){
				echo "Could not successfully run query ($query) from DB: " . mysql_error();
			}

		}
		else {            //auth is no	
			$query = "insert into log (Timestamp, RouterIP, RouterMAC, MAC, IP, Auth,  ChilliStatus) values ('$timestamp','$routerip','$routermac','$mac','$ip','$auth','online')";
			$result = $conn->query($query);	
		}	
	}
	
}
else{
	//This router has shamed us. Basically its useless. Its connected to the net
	//But ain't blocking them users. 
	//Chilli is offline, no updation and an alert should be generated.

	$query = "select * from log where RouterMAC='$routermac'";
	
	//check if the particular Router MAC was ever logged before
	
	$result = $conn->query($query);
	if ($result->num_rows > 0){ 	//some rows were found for the particular router MAC
		
		//delete all the rows, they have just one purpose left:- Deletion (MATRIX reference, B-) )
		
		$query = "delete from log where RouterMAC='$routermac'";
		$result = $conn->query($query);
		
		//now that all the rows are deleted, just insert a row now stating its dead
		
		$query = "insert into log (Timestamp, RouterIP, RouterMAC, ChilliStatus) values ('$timestamp','$routerip','$routermac','offline')";
		$result = $conn->query($query);
	}

	else{ //interesting, this router was never there or had no clients earlier, and chilli died.
	      //lets insert a row with info to say that this died a dishonourable death
		
		$query = "insert into log (Timestamp, RouterIP, RouterMAC, MAC, IP, Auth, UserName, SessionTime, MaxSessionTime, IdleTime, MaxIdleTime, TotalAccountTime, MaxTotalAccountTime, ChilliStatus) values ('$timestamp', '$routerip', '$routermac', '', '', '', '', 0, 0, 0, 0, 0, 0, 'offline')";		
		$result = $conn->query($query);
	}
	
}

echo "$routermac $routerip $numberOfNodes";

$mac = $obj->NodeList[0]->MAC;

$query = "select * from log where MAC='$mac'";
$result = $conn->query($query);
if ($result->num_rows > 0){
	print "Found it";
}
else{
	print "Nope";
}

echo "</h2></body> </html>";
?>

