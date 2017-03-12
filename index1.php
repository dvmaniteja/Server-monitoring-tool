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

#if(isset($_GET['submit'])){//to run PHP script on submit
	#if(!empty($_GET['check_list'])){
		#header("location:metrics1.php");
		#foreach($_GET['check_list'] as $selected){
			#echo $selected."</br>";
		#}
	#}
#}

?>

<!DOCTYPE html>
<html>

<head>

</head>

<body>
<form action="metrics1.php" method="GET">

<br><a class="active" href="index.php">Add Device</a></br>
<br><a href="addserver.php">Add Server</a></br>
<br><a href="deletedevice.php">Remove Device</a></br>
<br><a href="deleteserver.php">Remove Server</a></br>
<br><a href="index1.php">Monitor Servers</a></br>
<br><a href="device_interfaces.php">Monitor Devices</a></br>
<br><a href="dev-server.php">Monitor Both</a></br>

<center><h1 style="font-family:Trebuchet MS;font-size:250%">Monitoring servers</h1></center>
<center><h3 style="font-family:Trebuchet MS;font-size:150%">Multiple servers can be selected</h3></center>
<div style = "position: absolute; left:500px;top:200px;">

<?php

print "<table align=right width=580px align=bottom border cellpadding=10>"; 
print "<br><br><br><br><br><br><br><br><br><br><br><br><br><tr>";
print "<td>IP</td>";
print "<td>SELECT</td>";
print "</tr>";


$data1 = mysqli_query( $link10,"SELECT * FROM mani_SERVERS") or die(mysqli_error()); 

while ($row = mysqli_fetch_array($data1))
			{

			print "<tr>"; 
			print "<td>'$row[1]'<br></td>";
			print "<td><input type='checkbox' name='check_list[]' value='Server-$row[1].rrd'/></td>";
			#print "<td><input type='submit' name='view' value='view'></td>";
			#print "<td>'$row[0]'</td>";
			}

print "<tr><td><input type='submit' value='submit' name='submit'></td></tr>";
print"</table><br><br>";   
 
?>


		
</form>
</body>
</html>
