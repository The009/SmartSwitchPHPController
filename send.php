<?php
/*
*   SmartSwitchPHPController Copyright (C) 2018 The009
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*   Please send queries to the009@gmail.com
*/

##############
#  OPTIONS  #
#############

//: Enable Debug Ouput (Works better in CLI mode))
define('debug', false);

//Define the Time-Zone for your system to set the clocks on the switches. : https://www.php.net/manual/en/timezones.php
define('timeZone', 'America/New_York');

//Define The Version Number For the project due to everything requireing send.php
define('versionNumber', "1.1.2.7");

//No Further Options
$csv = array();
$devicesDisplayed = false;
$group = "";
if(getenv('SERVER_ADDR') == null){
  if($argv[1] == "group"){
    $group = $argv[2];
    $action = $argv[3];
    $ip = "";
    $port = "";
    $deviceType = "";
    $dimmerValue = "";
  }
  else{
    $ip = $argv[1];
    $port = $argv[2];
    $action = $argv[3];
    $deviceType = $argv[4];
    if($action == "dimmerAdjust"){
      $dimmerValue = $argv[5];
      $rawCommand = "";
    }else if(count($argv) == 6){
        $rawCommand = $argv[5];
        $dimmerValue = "";
    }else if($action == "connecttonetwork"){
      $rawCommand = $argv[5];
      $dimmerValue = $argv[6];
    }else{
      $rawCommand = "";
      $dimmerValue = "";
    }
  }
}
else{
  $ip = isset($_GET['ip']) ? $_GET['ip'] : '';
  $port = isset($_GET['port']) ? $_GET['port'] : '';
  $action = isset($_GET['action']) ? $_GET['action'] : '';
  $deviceType = isset($_GET['deviceType']) ? $_GET['deviceType'] : '';
  $group = isset($_GET['group']) ? $_GET['group'] : '';
  $rawCommand = isset($_GET['rawCommand']) ? $_GET['rawCommand'] : '';
  $dimmerValue = isset($_GET['dimmerValue']) ? $_GET['dimmerValue'] : '';
}

if(debug){
  echo("IP: " . $ip ."\n");
  echo("Port: " . $port . "\n");
  echo("Action: " . $action . "\n");
  echo("DevTyp: " . $deviceType . "\n" );
  if($dimmerValue)echo("Dimmer Value: " . $dimmerValue . "\n");
}

if($group != "" && $action != ""){
if(debug)echo("Sending Group: " . $group . "\n Action: " . $action . "\n");
if($group)if(preg_match("/^[a-zA-Z0-9 \s]+$/", $group) == 1){} else { die("$group is not a valid Group"); }
if($action)if(preg_match("/^[a-zA-Z]+$/", $action) == 1){} else { die("$action is not a valid action"); }

groupSend($action, $group);
}
else{
  if($ip)if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE)){} else { die("".$ip." is not a valid IP address");}
  if($port)if(($port >= 1) && ($port <= 65535)){} else { die("$port is not a valid port");}
  if($action)if(preg_match("/^[a-zA-Z]+$/", $action) == 1){} else { die("$action is not a valid action"); }
  if($deviceType)if(preg_match("/^[a-zA-Z0-9]+$/", $deviceType) == 1){} else { die("$deviceType is not a valid DeviceType"); }
  if($action == "raw"){if(json_decode($rawCommand) != null ){} else { die("Your Raw Command dose not appear to be valid JSON!");}}
  if($dimmerValue && $action != "connecttonetwork")if(!is_numeric($dimmerValue)) die("Dimmer Value dose not appear to be numeric.");

  if( $ip && $port && $action && $deviceType != ""){
    send($action, $deviceType, $ip, $port, $rawCommand, $dimmerValue);
  }
}

function groupSend($action, $group){
  foreach(getDevices() as $i => $item){
    if($item["group"] == $group){
      if(debug)echo("Found Group '" . $group . "'\n");
        send($action, $item["deviceType"], $item["deviceIP"], $item["devicePort"], "");
      }
    if($group == "all"){
      if(debug)echo("Found Group '" . $group . "'\n");
        send($action, $item["deviceType"], $item["deviceIP"], $item["devicePort"], "");
      }
      else{
        if(debug)echo("Group '". $item["group"] . "' Is not a match for Group '" . $group . "'\n");
      }
  }
}

function getDevices(){
	if(file_exists("devices.csv")){
    $csv = array_map('str_getcsv', file("devices.csv"));
    array_walk($csv, function(&$a) use ($csv){
      $a = array_combine($csv[0], $a);
	  });
    array_shift($csv);
	}
	else{
	   die("No Confing CSV Found");
	}
    return $csv;
}

function send($command , $plugType, $ip, $port, $rawCommand = NULL, $dimmerValue = NULL)
{
  $date = new DateTime("now", new DateTimeZone(timeZone));
  switch(strtolower($command)) {
    case "on": $payload = '{"system":{"set_relay_state":{"state":1}}}';
    break;
    case "off": $payload = '{"system":{"set_relay_state":{"state":0}}}';
    break;
    case "sysinfo": $payload = '{"system":{"get_sysinfo":null}}';
    break;
    case "ledoff": $payload = '{"system":{"set_led_off":{"off":1}}}';
    break;
    case "ledon": $payload = '{"system":{"set_led_off":{"off":0}}}';
    break;
    case "dimmeradjust": $payload = '{"smartlife.iot.dimmer":{"set_brightness":{"brightness":'. $dimmerValue . '}}}';
    break;
    case "scannetwork": $payload = '{"netif":{"get_scaninfo":{"refresh":1}}}';
    break;
    case "connecttonetwork": $payload = '{"netif":{"set_stainfo":{"ssid":"'. $rawCommand .'","password":"'. $dimmerValue .'","key_type":3}}}';
    break;
    case "updatetime": $payload = '{"time":{"set_timezone":{"year":'.$date->format('Y').',"month":'.$date->format('n').',"mday":'.$date->format('j').',"hour":'.$date->format('G').',"min":'.$date->format('i').',"sec":'.$date->format('s').',"index":42}}}';
    break;
    case "raw": $payload = $rawCommand;
    break;
    case "" : die("No Command");
    break;
    default:
    die("Action error");
    break;
  }
  if(debug){echo("Un-Encoded Payload: " . $payload . "\n");}

  if($plugType == "HS105" || $plugType == "HS220" ){
    if(debug){echo("Using HS105/HS220 Encryption \n");}
      $key = 171;
		  $message = "\0\0\0" . chr(strlen($payload));
		  foreach (str_split($payload) as $cnt1) {
        $a = $key ^ ord($cnt1);
        $key = $a;
        $message .= chr($a);
		  }
  }
	else{
    if(debug){echo("Using HS100/HS110 Encryption \n");}
    $key = 171;
    $message = "\0\0\0\0";
		foreach (str_split($payload) as $cnt1) {
      $a = $key ^ ord($cnt1);
      $key = $a;
      $message .= chr($a);
    }
  }

  if(debug && $rawCommand != ""){ 	echo("rawCommand: " . $rawCommand . "\n"); }
	if(debug){ 	echo("rawSentData: " . $message . "\n"); }

  if (!($sock = socket_create(AF_INET, SOCK_STREAM, 0))) {
    $errorCode = socket_last_error();
    $errMsg = socket_strerror($errorCode);
    die("Couldn't create socket: [{$errorCode}] {$errMsg} \n");
  }

	if (!socket_connect($sock, $ip, $port)) {
    $errorCode = socket_last_error();
    $errMsg = socket_strerror($errorCode);
    die("Could not connect: [{$errorCode}] {$errMsg} \n");
  }

  if (!socket_send($sock, $message, strlen($message), 0)) {
    $errorCode = socket_last_error();
    $errMsg = socket_strerror($errorCode);
    die("Could not send data: [{$errorCode}] {$errMsg} \n");
  }

  $buff = 'Buffer STRING';

  if (false !== ($bytes = socket_recv($sock, $buff, 1024, 0))) {
    if(debug){echo "Read {$bytes} bytes of socket_recv(). \n";}
  }
  else{
    if(debug){echo "socket_recv() error; Reason: " . socket_strerror(socket_last_error($socket)) . "\n";}
  }

  if(debug){echo "Closing Socket \n";}
  socket_close($sock);
  $sendResult = json_decode(json_encode(decode($buff))) ."\n";
  if(debug){echo $sendResult;}
}

function decode($encodedMsg)
{
  $encodedMsg = substr($encodedMsg, 4);
  $key = 171;
  $message = "";
	foreach (str_split($encodedMsg) as $cnt2) {
    $a = $key ^ ord($cnt2);
    $key = ord($cnt2);
    $message .= chr($a);
  }
  return $message;
}
?>
