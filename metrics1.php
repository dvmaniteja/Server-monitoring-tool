<?php 

session_start();

$path=realpath(dirname(__FILE__) . '/..');
#echo $path;
$final_path="$path/db.conf";

$handle = fopen($final_path, "r");

while (!feof($handle))
         {
            $line = fgets($handle);
 
            $data = explode('"',$line);
 
                if($data[0]=='$host=')
                {
                  $host= $data[1];
                }
								elseif($data[0]=='$port=')
                {
                 $port= $data[1];
                }
								elseif($data[0]=='$database=')
                {
                 $database= $data[1];
                }
                elseif($data[0]=='$username=')
                {
                 $username= $data[1];
                }
                elseif($data[0]=='$password=')
                {
                 $password= $data[1];
                }   
         }

$link0 = mysqli_connect("$host","$username","$password");

    if($link0 === false)
    {

        die("ERROR: . " . mysqli_connect_error());

    }

$link10 = mysqli_connect("$host","$username","$password","$database");

    if($link10 === false)
    {

        die("ERROR: . " . mysqli_connect_error());

    }

$_SESSION['check_list']=$_GET['check_list'];

#=$_GET['check_list'];

#foreach($servers as $selected){
			#echo $selected;
	#	}

?>

<!DOCTYPE html>
<html>

<head>

</head>

<body>
<form action="graph1_options.php" method="GET">

<br><a class="active" href="index.php">Add Device</a><br>
<br><a href="addserver.php">Add Server</a><br>
<br><a href="deletedevice.php">Remove Device</a><br>
<br><a href="deleteserver.php">Remove Server</a><br>
<br><a href="index1.php">Monitor Servers</a><br>
<br><a href="device_interfaces.php">Monitor Devices</a><br>
<br><a href="dev-server.php">Monitor Both</a><br>


<center><h1 style="font-family:Trebuchet MS;font-size:250%">METRICS</h1></center>
<center><h3 style="font-family:Trebuchet MS;font-size:250%">You can select multiple Metrics</h3></center>
<div style = "position: absolute; left:500px;top:200px;">
	<table align=center border cellpadding=10>
<br><br><br><br><br><br><br><br><br><br><br><br>
<tr>
			<td>CPU USAGE</td>
			<td><input type='checkbox' name='check_list1[]' value='cpuusage'></td>
		</tr>
		
		<tr>
			<td>Bytes Per Second</td>
			<td><input type='checkbox' name='check_list1[]' value='bytespersec'></td>
		</tr>

		<tr>
			<td>Bytes Per Request</td>
			<td><input type='checkbox' name='check_list1[]' value='bytesperreq'></td>
		</tr>

		<tr>
			<td>Request Per Second</td>
			<td><input type='checkbox' name='check_list1[]' value='reqpersec'></td>
		</tr>

		<tr><td><input type='submit' value='submit' name='submit'></td></tr>
	</table>

</form>
</body>
</html>
