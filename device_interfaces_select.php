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



?>

<!DOCTYPE html>
<html>

<head>

</head>

<body>
<form action="metrics2.php" method="GET">


<br><a class="active" href="index.php">Add Device</a><br>
<br><a href="addserver.php">Add Server</a><br>
<br><a href="deletedevice.php">Remove Device</a><br>
<br><a href="deleteserver.php">Remove Server</a><br>
<br><a href="index1.php">Monitor Servers</a><br>
<br><a href="device_interfaces.php">Monitor Devices</a><br>
<br><a href="dev-server.php">Monitor Both</a><br>


<center><h1 style="font-family:Trebuchet MS;font-size:250%">DEVICES</h1></center>
<center><h3 style="font-family:Trebuchet MS;font-size:250%">You can select multiple Devices</h3></center>


<?php

print "<table align=center border cellpadding=10>"; 
print "<br><tr>";
print "<td>IP</td>";
print "<td>PORT</td>";
print "<td>COMMUNITY</td>";
print "<td>INTERFACES SELECTED FOR VIEWING</td>";
print "<td>SELECT</td>";
print "</tr>";


$data1 = mysqli_query( $link10,"SELECT IP,PORT, COMMUNITY, SELECTED_INTERFACES FROM mani_DEVICES_INTERFACES_SELECTED") or die(mysqli_error()); 

$devv="Device";
while ($row = mysqli_fetch_array($data1))
			{

			print "<tr>"; 
			print "<td>'$row[0]'<br></td>";
			print "<td>'$row[1]'<br></td>";
			print "<td>'$row[2]'<br></td>";
			print "<td>'$row[3]'<br></td>";
			print "<td><input type='checkbox' name='check_list[]' value='$devv-$row[0]-$row[1]-$row[2]'/></td>";
			#print "<td><input type='submit' name='view' value='view'></td>";
			#print "<td>'$row[0]'</td>";
			}

print "<tr><td><input type='submit' value='submit' name='submit'></td></tr>";
print"</table><br><br>";   
 
?>
	
</form>
</body>
</html>
