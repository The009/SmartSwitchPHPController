# SmartSwitchPHPController
A CLI or Web Interface written in PHP Using Sockets to directly control TP-Link Smart Switches

**Requires PHP With Socksts** Web Interface Requires A Web Server


## Options

**To add devices to the web interface EDIT/Follow The example of devices.csv**
*The background color of the device card is the color option in the csv use HTML color codes*
```
deviceName,deviceIP,devicePort,deviceType,group,color
Office Light,10.0.0.181,9999,HS105,,
Living Room Light,10.0.0.179,9999,HS200,Living Room Lights,lightBlue
Living Room Lamp,10.0.0.178,9999,HS105,Living Room Lights,lightBlue
Dining Room,10.0.0.123,9999,HS220,,
```

**To Enable Debug Edit send.php**

Find
>define('debug', false);

to
>define('debug', true);

it will just echo out some useful information.

**Debug Mode works better in CLI**

## Examples ("" are mandatory for things with spaces in them otherwise you can usually ignore them)

Usage: Single Switch Example
>php.exe send.php "IP" "Port" "Command(On/Off)" "Device Type"

Usage: Turn On a single switch.
>php.exe send.php "10.0.0.178" "9999" "Off" "HS200"

Usage: Group usage example.
>php.exe send.php "group" "group name" "action"

Usage: To turn on just the group of "Living Room Lights"
>php.exe send.php "group" "Living Room Lights" "on"

Usage: To turn on every light in the config
>php.exe send.php "group" "all" "on"

Usage: Dimmer Function On HS220
>php.exe send.php "IP" "Port" "dimmerAdjust" "HS220" "Dimmer Value"

Usage Example: Dimmer Function On HS220
>php.exe send.php "10.0.0.123" "9999" "dimmerAdjust" "HS220" "100"

**RAW Commands can be send via CLI**
Usage:
>php.exe send.php "10.0.0.178" "9999" "raw" "HS105" {\"system\":{\"get_sysinfo\":null}}


When a RAW command is used the reply from the device will be given.

### Using raw sends whatever you want directly to the device BE CAREFUL!

---

# CLI Windows Help

The Smallest you can get this to run on windows is 5 files. *Unless you have php with sockets already then its 1 file **send.php***


[Written / Tested on PHP 7.2](https://windows.php.net/download#php-7.2)

Download Thread Safe x86/64 Version (Recommended 64bit on a 64bit OS)

Extract:
  * php.exe
  * php7ts.dll
  * ext/php_sockets.dll

Put them where you wish this to run from IE:
>C:\ManualPrograms\SmartSwitchPHPController\

You will need to create a "php-cli.ini" file with the contents of:
```
[PHP]
extension=php_sockets.dll
extension_dir = "."
```

If you want the php_sockets.dll to be in a folder change ```extension_dir = "." ```to ```extension_dir = "folderName/"```

Then you are done. Open your fav CMD line app and use the usage examples above.
