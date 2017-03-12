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

$_SESSION['check_list3']=$_GET['check_list3'];

#$b=$_SESSION['check_list2'];

#echo $b[0];

?>

<!DOCTYPE html>
<html>

<head>

</head>

<body>
<form action="graph3_options.php" method="GET">

<br><a class="active" href="index.php">Add Device</a><br>
<br><a href="addserver.php">Add Server</a><br>
<br><a href="deletedevice.php">Remove Device</a><br>
<br><a href="deleteserver.php">Remove Server</a><br>
<br><a href="index1.php">Monitor Servers</a><br>
<br><a href="device_interfaces.php">Monitor Devices</a><br>
<br><a href="dev-server.php">Monitor Both</a><br>


<center><h1 style="font-family:Trebuchet MS;font-size:250%">SELECT ANY ONE DEVICE METRIC</h1></center>

<div style = "position: absolute; left:400px;top:150px;">

	<table style="float: left;" align=center border cellpadding=10>
		<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<tr>
			<td>IN</td>
			<td><input type='checkbox' name='check_list4[]' value='in'></td>
		</tr>
		
		<tr>
			<td>OUT</td>
			<td><input type='checkbox' name='check_list4[]' value='out'></td>
		</tr>

		<tr>
			<td>Both Input & Output</td>
			<td><input type='checkbox' name='check_list4[]' value='inout'></td>
		</tr>

		<tr>
			<td>Aggregated Input & Output</td>
			<td><input type='checkbox' name='check_list4[]' value='aggr'></td>
		</tr>

		<tr>
			<td>All</td>
			<td><input type='checkbox' name='check_list4[]' value='all'></td>
		</tr>

		<tr><td><input type='submit' value='submit' name='submit'></td></tr>
	</table>


		
</form>
</body>
</html>
