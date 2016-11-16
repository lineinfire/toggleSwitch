<?php
include ("PHPTelnet.php");

$server = '192.168.7.229';
$user ='admin';
$pass = 'ATI#123';

$telnet = new PHPCiscoTelnet();
$result = $telnet->Connect($server, $user, $pass);

$telnet->enable('ATI#123');

$telnet->DoCommand("configure terminal");


if (isset($_GET['action']) && isset($_GET['portNumber'])) {
	
	print "<h3>Shutting Down Port ".$_GET['portNumber']."</h3><br />";
	if ($result == 0) {

		$portNumber = $_GET['portNumber'];
        	// print $portNumber."--";
        	// exit;
		
		$command = "int FastEthernet0/".$portNumber;
        	// print $command."==";
        	// exit;
		
		$flag = $telnet->DoCommand($command);
		if($flag == 1)
		{
			print "<div style='width:30%' class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>".$command. " has been executed successfully.</div><br />";
		} else
		{
			print "<div style='width:25%' class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>".$command. " execution failed.</div><br />";
		}
		$checkStatus = $telnet->DoCommand("shutdown");	
        	// print $checkStatus."-->";
		
	}
} elseif (isset($_GET['portNumber'])) {					
		# code...
	print "<h3>Restarting Port ".$_GET['portNumber']."</h3><br />";
	if ($result == 0) {

		$portNumber = $_GET['portNumber'];
        	// print $portNumber."--";
        	// exit;
		$command = "int FastEthernet0/".$portNumber;
        	// print $command."==";
        	// exit;
		$flag = $telnet->DoCommand($command);
		if($flag == 1)
		{
			print "<div style='width:30%' class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>".$command. " has been executed successfully.</div><br />";
		} else
		{
			print "<div style='width:25%' class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>".$command. " execution failed.</div><br />";
		}
		$telnet->DoCommand("no shutdown");	
        	//}            
	}

}

$telnet->Disconnect();	
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html" />
	<meta name="author" content="lineinfire" />
	<title>
		Toggle Switch for Cisco Router
	</title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css">
	<link href="toggle.css" rel="stylesheet">
	<style>
		body {
			margin:0px;
			padding:10px;
		}
	</style>
</head>
<body>
	<div style="width: 50%;">
		<form>
			<div class="form-group">
				<select class="selectpicker" data-live-search="true" name="portNumber">
					<option data-tokens="saroj shrestha" value="7">
						Saroj Shrestha
					</option>
					<option data-tokens="Bishal Ghimire" value="5">
						Bishal Ghimire
					</option>
					<option data-tokens="khadkaji Arun" value="4">
						Arun Neupane
					</option>
				</select>
			</div>
			<div class="form-group">
				<div class="onoffswitch">
					<input type="checkbox" name="action" class="onoffswitch-checkbox" id="myonoffswitch" checked>
					<label class="onoffswitch-label" for="myonoffswitch">
						<span class="onoffswitch-inner">
						</span>
						<span class="onoffswitch-switch">
						</span>
					</label>
				</div>
			</div>
			<button type="submit" class="btn btn-default">
				Perform Action
			</button>
		</form>
	</div>
	<script src="https://code.jquery.com/jquery-3.1.1.min.js">
	</script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js">
	</script>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css">
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/bootstrap-select.min.js">
	</script>
</body>
</html>