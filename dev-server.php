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
<form method="GET">

<br><a class="active" href="index.php">Add Device</a><br>
<br><a href="addserver.php">Add Server</a><br>
<br><a href="deletedevice.php">Remove Device</a><br>
<br><a href="deleteserver.php">Remove Server</a><br>
<br><a href="index1.php">Monitor Servers</a><br>
<br><a href="device_interfaces.php">Monitor Devices</a><br>
<br><a href="dev-server.php">Monitor Both</a><br>

<center><h2>Enter interfaces you want to view for each device separated by commas</h2></center>
<center><h2>(Click submit for each of the corresponding Device)</h2></center>

<?php

print "<table align=center border cellpadding=10>"; 
print "<br><tr>";
print "<td>IP</td>";
print "<td>PORT</td>";
print "<td>COMMUNITY</td>";
print "<td>SELECTED INTERFACES</td>";
print "<td>ENTER INTERFACES TO VIEW</td>";
print "<td>SUBMIT</td>";
print "</tr>";


$data1 = mysqli_query( $link10,"SELECT IP,PORT, COMMUNITY, interfaces FROM mani_DEVICES") or die(mysqli_error()); 
$i=0;
while ($row = mysqli_fetch_array($data1))
			{

			print "<tr>"; 
			print "<td>'$row[0]'<br></td>";
			print "<td>'$row[1]'<br></td>";
			print "<td>'$row[2]'<br></td>";
			print "<td>'$row[3]'<br></td>";
			print "<td><input type=text size=30 name='interface$i'></td>";
			print "<td><input type='submit' name='submit$i' value='enter'></td>";
			#print "<td><input type='submit' name='view' value='view'></td>";

					$string=$_GET["interface$i"];
								if(isset($_GET["submit$i"])){
									#print ($username);
									#echo "hi";

									#$sql0="SELECT * FROM A2_DEVICES_INTERFACES_SELECTED WHERE IP='$row[0]' and PORT='$row[1]' and COMMUNITY='$row[2]' AND BOTHS IS NULL";
									#$result0=mysqli_query($link10, $sql0);
									#$rowcount=mysqli_num_rows($result0);
									#echo $rowcount;

									#if($rowcount==0) {
									#$sql1="INSERT INTO A2_DEVICES_INTERFACES_SELECTED(IP, PORT, COMMUNITY, BOTHS) values('$row[0]','$row[1]', '$row[2]','$string' )"; 
									#mysqli_query($link10, $sql1);
									#}

									#else {
									$sql2="UPDATE mani_DEVICES_INTERFACES_SELECTED SET BOTHS = '$string' WHERE IP='$row[0]' and PORT='$row[1]' and COMMUNITY='$row[2]'";
									mysqli_query($link10, $sql2);
									#}
																	}

			#print "<td>'$row[0]'</td>";
			$i++;
			}

print"</table><br><br>";   
 
?>


	<br> <br>
<a href="index7.php">Click to go to next page</a>

</form>
</body>
</html>
