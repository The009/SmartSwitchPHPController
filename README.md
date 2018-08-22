# SmartSwitchPHPContoller
A CLI or Web Interface written in PHP Using Sockets to directly control TP-Link Smart Switches

**Requires PHP With Socksts** Web Interface Requires A Web Server


## Options

**To add devices to the web interface EDIT/Follow The example of devices.csv**
```
deviceName,deviceIP,devicePort,deviceType
Living Room Light,10.0.0.100,9999,HS200
Living Room Lamp,10.0.0.105,9999,HS105
Bedroom Light,10.0.0.102,9999,HS200
```

**To Enable The Command Line Interface Edit index.php**

Find 
>define('cli', false); 

change it to 
>define('cli', true);

If you are sending commands and nothing is happening try changing 
>define('debug', false);

to
>define('debug', true);

it will just echos out some useful information.

**Debug Mode works better in CLI**

## Examples

Usage: 
>php.exe index.php "IP" "Port" "Command(On/Off)" "Device Type"

Usage:
>php.exe index.php "10.0.0.178" "9999" "Off" "HS105"

**RAW Commands can be send via CLI**
Usage: 
>php.exe index.php "10.0.0.178" "9999" "raw" "HS105" {\"system\":{\"get_sysinfo\":null}}


When a RAW command is used the reply from the device will be given.

### Using raw sends whatever you want directly to the device BE CAREFUL!

---

# CLI Windows Help

The Smallest you can get this to run on windows is 5 files. *Unless you have php with sockets already then its 1 **index.php** file*


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

---
# TODO 

Link Multiple Devices On One Click