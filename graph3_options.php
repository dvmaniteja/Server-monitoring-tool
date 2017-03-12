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

$_SESSION['check_list4']=$_GET['check_list4'];


#$a=$_SESSION['check_list1'];
#echo $a[0];
#$servers=$_GET['check_list'];

#foreach($servers as $selected){
			#echo $selected;
	#	}

#$c=$_SESSION['check_list2'];

#echo $c[0];

?>

<!DOCTYPE html>
<html>

<head>

</head>

<body>
<form action="graph3.php" method="GET">

<br><a class="active" href="index.php">Add Device</a><br>
<br><a href="addserver.php">Add Server</a><br>
<br><a href="deletedevice.php">Remove Device</a><br>
<br><a href="deleteserver.php">Remove Server</a><br>
<br><a href="index1.php">Monitor Servers</a><br>
<br><a href="device_interfaces.php">Monitor Devices</a><br>
<br><a href="dev-server.php">Monitor Both</a><br>


<center><h1 style="font-family:Trebuchet MS;font-size:250%">GRAPH OPTIONS</h1></center>
<center><h3 style="font-family:Trebuchet MS;font-size:250%">You can select any one option</h3></center>
<div style = "position: absolute; left:500px;top:200px;">
	<table align=center border cellpadding=10>
		<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<tr>
			<td>HOURLY GRAPH</td>
			<td><input type='checkbox' name='check_list5[]' value='-1h'></td>
		</tr>
		
		<tr>
			<td>DAILY GRAPH</td>
			<td><input type='checkbox' name='check_list5[]' value='-1d'></td>
		</tr>

		<tr>
			<td>WEEKLY GRAPH</td>
			<td><input type='checkbox' name='check_list5[]' value='-1w'></td>
		</tr>

		<tr>
			<td>MONTHLY GRAPH</td>
			<td><input type='checkbox' name='check_list5[]' value='-1m'></td>
		</tr>

			<tr>
			<td>YEARLY GRAPH</td>
			<td><input type='checkbox' name='check_list5[]' value='-1y'></td>
		</tr>

		<tr><td><input type='submit' value='submit' name='submit'></td></tr>
	</table>

		
</form>
</body>
</html>
