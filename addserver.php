<!DOCTYPE html>
<html>

<head>


<br><a class="active" href="index.php">Add Device</a></br>
<br><a href="addserver.php">Add Server</a></br>
<br><a href="deletedevice.php">Remove Device</a></br>
<br><a href="deleteserver.php">Remove Server</a></br>
<br><a href="index1.php">Monitor Servers</a></br>
<br><a href="device_interfaces.php">Monitor Devices</a></br>
<br><a href="dev-server.php">Monitor Both</a></br>

 <h3> Enter IP of Server</h3> 
<form action="addserver.php" method="post">
IP:        <input type="text" name="serverip" required><br>
<br>
<input type="submit" value="Enter">
</form>

<?php

if(!empty($_POST["serverip"])) {
 $x= $_POST["serverip"];  


#require "find.php";
$myfile = fopen("../db.conf", "r") or die("Unable to open file!");
eval(fread($myfile,filesize("../db.conf")));
fclose($myfile);

$conn = mysqli_connect($host,$username, $password,$database,$port);

// Check connection
if (!$conn) {
   die("Connection failed: " . mysqli_connect_error());
}
//echo "Connected successfully<br>";
mysqli_select_db($conn,"$database");

$tbl = "CREATE TABLE IF NOT EXISTS mani_SERVERS ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                server VARCHAR(255) NOT NULL,
                PRIMARY KEY (id),
                UNIQUE (id,server) 
                )"; 

$query = mysqli_query($conn, $tbl); 

if ($query === TRUE) {
	#echo "<h3>blockedusers table created OK :) </h3>"; 
} else {
	echo "<h3>blockedusers table NOT created :( </h3>"; 
}
$sqls = "INSERT INTO mani_SERVERS (server) VALUES (\"$x\")";

if (mysqli_query($conn, $sqls)) {
    echo "Server Added";
} else {
 echo "Server Exists ALready";
    echo "Error: " . $sqls . "<br>" . mysqli_error($conn);
}
}

?>
</head>
</html>
