#!/bin/bash

router_ip=$(wget http://ipinfo.io/ip -qO -)
router_mac=$(ifconfig | grep eth0 | head -n1 | sed -r 's/.*(.{19})/\1/'| cut -c 1-17)
router_time=$(TZ=IST-5:30 date)
echo "{\"Timestamp\":\""$router_time"\",\"RouterIP\":\""$router_ip"\"" > output.json
echo ",\"RouterMAC\":\""$router_mac"\", \"NodeList\":[" >> output.json

first_line=$(chilli_query list 2>&1)

echo $first_line
echo $first_line
echo "hi"
if [[ $first_line == *"connect: Connection refused"* ]]; then
	echo "], \"Chilli_Status\":\"offline\"}" >> output.json
	exit
fi

chilli_query list | while read line; do
	mac=$(echo $line | cut -d ' ' -f 1 |sed 's/-/:/g')
	ip=$(echo $line | cut -d ' ' -f 2)
	status=$(echo $line | cut -d ' ' -f 5)
	echo "{\"MAC\":\""$mac"\",\"IP\":\""$ip"\"" >> output.json
	if [ $status -eq 1 ]; then 
		user=$(echo $line | cut -d ' ' -f 6)
		session_time=$(echo $line | cut -d ' ' -f 7 | cut -d '/' -f 0)
		max_session_time=$(echo $line | cut -d ' ' -f 7 | cut -d '/' -f 2)
		idle_time=$(echo $line | cut -d ' ' -f 8 | cut -d '/' -f 0)
		max_idle_time=$(echo $line | cut -d ' ' -f 8 | cut -d '/' -f 2)
		total_account_time=$(echo $line | cut -d ' ' -f 9 | cut -d '/' -f 0)
		max_total_account_time=$(echo $line | cut -d ' ' -f 9 | cut -d '/' -f 2)
		total_idle_time=$(echo $line | cut -d ' ' -f 10 | cut -d '/' -f 0)
		max_total_idle_time=$(echo $line | cut -d ' ' -f 10 | cut -d '/' -f 2)
		echo ",\"Auth\":\"yes\",\"UserName\":\""$user"\",\"SessionTime\":\""$session_time"\",\"Max Session Time\":\""$max_session_time"\",\"Idle Time\":\""$idle_time"\",\"Max Idle Time\":\""$max_idle_time"\",\"Total Account Time\":\""$total_account_time"\",\"Max Total Account Time\":\""$max_total_account_time"\"" >> output.json

	else 
		echo ",\"Auth\":\"no\"" >> output.json
	fi
	echo "}," >> output.json

done
echo "{}" >> output.json
echo "], \"Chilli_Status\":\"online\"}" >> output.json


curl -vxPOST -H "Content-type: application/json" -d @output.json http://54.169.247.144/log/
