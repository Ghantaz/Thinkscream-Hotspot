<?php

ini_set("date.timezone", "Asia/Kolkata");

//Parameters for sql connection
//common
$servername = "localhost";
$username = "root";
$password = "raspbian";

//databases
$logdata = "logdata";
$radius = "radius";

//make connections
$logconn = new mysqli($servername, $username, $password, $logdata);
$radconn = new mysqli($servername, $username, $password, $radius);

//Lets rumble
echo "<html> 
<head>
<link rel='stylesheet' href='styles.css'> 
<title> Thinkscream Hotspot Web Page </title>
</head> 

<body>

<div class='header'>
<center><h1> Thinkscream Hotspot Monitor </h1> 
</div>
<div id='scrollable'>
<table class='TFtable'>
<thead><tr id='tablehead'><td>Router MAC</td><td>Router IP</td><td>Timestamp</td><td>Online?</td><td>No. of Users</td><td>Client MAC</td><td>Client IP</td><td>Client OS</td><td>User Name</td>
	<td>Session Time</td><td>Idle Time</td><td>Total Account Time</td><td>Total Download</td><td>Total Upload</td></tr></thead>";
//First gather all different routers from log table in logdata

$query = "select distinct RouterMAC from log";
$result = $logconn->query($query);
$routers_mac = array();
$routers_ip = array();
$routers_timestamp = array();
$routers_status = array();
$num_routers = $result->num_rows;

while ($row = $result->fetch_assoc()){
	$routers_mac[] = $row;
	//Also get and store the IP addresses of each router
	$mac = $row['RouterMAC'];
	$query = "select RouterIP from log where RouterMAC='$mac'";
	$ipresult = $logconn->query($query);
	//get the first row only, others not required
	$row = $ipresult->fetch_assoc();
	$routers_ip[] = $row;
	$ipresult->free();
	
	//now for timestamp
	$query = "select Timestamp from log where RouterMAC='$mac'";
        $time_result = $logconn->query($query);
        $row = $time_result->fetch_assoc();
        $routers_timestamp[] = $row;
	$time_result->free();
	
	//Chill Status
	$query = "select ChilliStatus from log where RouterMAC='$mac'";
        $status_result = $logconn->query($query);
        $row = $status_result->fetch_assoc();
        $routers_status[] = $row;
        $status_result->free();	
}

//Got all router info 
//print_r($routers_mac);
//print_r($routers_ip);
//print_r($routers_timestamp);
//print_r($routers_status);
//print_r($num_routers);

//if chilli status is offline or time is too far off, more than 5 mins
for($i=0; $i<$num_routers; $i++){
	$timestamp = new DateTime();
        $routertime = new DateTime($routers_timestamp[$i]['Timestamp']);
        $interval = $timestamp->diff($routertime);
        $elapsed = $interval->format('%i');
        $elapsed = (int)$elapsed;

	if($routers_status[$i]['ChilliStatus'] == "offline"){
		echo "<tr style='background:#800000; color:white;'><td>". $routers_mac[$i]['RouterMAC'] ."</td><td>". $routers_ip[$i]['RouterIP'] ."</td><td>". $routers_timestamp[$i]['Timestamp'] . "</td><td>No (Portal Offline)</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>";
	}
	elseif ($elapsed > 5){
		echo "<tr style='background:#800000; color:white;'><td>". $routers_mac[$i]['RouterMAC'] ."</td><td>". $routers_ip[$i]['RouterIP'] ."</td><td>". $routers_timestamp[$i]['Timestamp'] . "</td><td>No (Router Offline)</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>";
	}
	
}

echo "</table></div>";

echo"
</center>
</body>

</html>";

?>

