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
require_once('send.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A PHP Script and Web Interface for Smart Switches">
    <meta name="author" content="The009">

    <title>Smart Switch PHP Controller</title>
    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/heroic-features.css" rel="stylesheet">
    <!-- Custom Icon for this template -->
	  <link rel="icon" type="image/png" href="vendor/icon/48px-System-shutdown.svg.png"/>
    <!-- Java Script Load of JQuery  -->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- JavaScript for sending of data  -->
    <script type="text/javascript">
    $( document ).ready(function() {
      $("#dimmverValue{$i}").change(function() {
        $.ajax({
          url: 'send.php',
          type: 'get',
          data: $("#form{$i}").serialize(),
          success: function(response){
            console.log("Enable debug in send.php for response");
            console.log( response );
          }
        });
      });
    });

    function SingleSend( givenIp, givenPort, reqAction, devTyp){
      var ip = givenIp;
      var port = givenPort;
      var action = reqAction;
      var deviceType = devTyp;
      return $.ajax({
        url: 'send.php',
        type: 'get',
        data: { ip: ip, port: port, action: action, deviceType: deviceType },
        });
      };

      function GroupSend( groupName, reqAction){
        var group = groupName;
        var action = reqAction;
        return $.ajax({
          url: 'send.php',
          type: 'get',
          data: { group: group, action: action},
          });
        };
  </script>

  </head>

  <body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="?">Smart Switch PHP Controller</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <div class="container">

    <!-- Page Features -->
    <div class="row text-center my-3">
<?php
function displayDeviceList($csv){
?>
      <div class='col-lg-3 col-md-6 mb-4'>
        <div class='card'>
          <div class='card-body'>
          <h4 class='card-title'>All Lights</h4>
          <p class='card-text'></p>
        </div>
        <div class='card-footer'>
          <a href="#" onclick="GroupSend('all', 'On').always(function(data){console.log(data)});" class='btn btn-primary'>On</a>
          <a href="#" onclick="GroupSend('all', 'Off').always(function(data){console.log(data)});" class='btn btn-primary'>Off</a>
        </div>
      </div>
    </div>
<?php
	 $groupNumbers = array_column($csv, 'group');
	 $unique = array_merge(array_flip(array_flip($groupNumbers)));
	 $groupedLights = array();
	 foreach ($unique as $key2 => $uniqueGroupIDs){
     if($uniqueGroupIDs != ""){

echo <<<EOD
	   <a name = "Group{$key2}"></a>
	   <div class='col-lg-3 col-md-6 mb-4'>
      <div class='card'>
  		  <div class='card-body'>
  		    <h4 class='card-title'>{$uniqueGroupIDs}</h4>
  				<p class='card-text'>
EOD;
       foreach($csv as $key => $value){
         if ($value["group"] == $uniqueGroupIDs && $value["group"] != "") {
           echo "" .$value['deviceName']. "<br>\n";
         }
       }
echo <<<EOD
          </p>
      </div>
      <div class='card-footer'>
        <a href="#Group{$key2}" onclick="GroupSend('{$uniqueGroupIDs}', 'On').always(function(data){console.log(data)});" class='btn btn-primary'>On</a>
        <a href="#Group{$key2}" onclick="GroupSend('{$uniqueGroupIDs}', 'Off').always(function(data){console.log(data)});" class='btn btn-primary'>Off</a>
      </div>
    </div>
  </div>
EOD;
}
  }

  foreach($csv as $i => $item) {
    $backgroundColor = ($item['color'] !="" ? $item['color'] : "#ffffff");
echo <<<EOD
    <a name = "{$i}"></a>
    <div class='col-lg-3 col-md-6 mb-4'>
      <div class='card'>
        <div class='card-body' style='background-color:{$backgroundColor}'>
          <h3 class='card-title'>{$item["deviceName"]}</h3>
          <p class='card-text'></p>
        </div>
        <div class='card-footer'>
          <a href="#{$i}" onclick="SingleSend('{$item["deviceIP"]}','{$item["devicePort"]}','On','{$item["deviceType"]}').always(function(data){console.log(data)});" class='btn btn-primary'>On</a> -
          <a href="#{$i}" onclick="SingleSend('{$item["deviceIP"]}','{$item["devicePort"]}','off','{$item["deviceType"]}').always(function(data){console.log(data)});" class='btn btn-primary'>Off</a>
EOD;
if($item['deviceType'] == "HS220"){
echo <<<EOD
      <p> </p>
      <div id="slider_container">
        <form id="form{$i}">
          <input id="deviceIP" name="ip" type="hidden" value="{$item["deviceIP"]}" />
          <input id="devicePort" name="port" type="hidden" value="{$item["devicePort"]}" />
          <input id="action" name="action" type="hidden" value="dimmerAdjust" />
          <input id="deviceType" name="deviceType" type="hidden" value="{$item["deviceType"]}" />
          <input id="dimmverValue{$i}" name="dimmerValue" type="range" min="1" max="100" step="1" value="50" />
        </form>
      </div>
EOD;
}
echo <<<EOD
        </div>
      </div>
    </div>
EOD;
}
  }

	if($_SERVER['SERVER_ADDR'] != null){
		;
			if(!$devicesDisplayed){
					$devicesDisplayed = true;
					displayDeviceList(getDevices());
			}
?>
  </div>
      <!-- /.row -->
</div>
    <!-- /.container -->

    <!-- Footer -->
<footer class="py-5 bg-dark">
  <div class="container">
    <p class="m-0 text-center text-white">Copyright &copy; The009 <a href="https://www.the009.net" target="_blank">www.the009.net</a> 2018</p>
	  <p class="m-0 text-center text-white"><a href="https://programs.the009.net/SmartSwitchPHPController/" target="_blank">Version <?php echo versionNumber;?></a><br />
	     This work is licensed under <a href="https://www.gnu.org/licenses/gpl-3.0.en.html">The GNU General Public License v3.0</a>.
    </p>
  </div>
  <!-- /.container -->
  </footer>
  <!-- Bootstrap core JavaScript -->
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php }?>
