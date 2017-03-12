<!DOCTYPE html>
<html>

<head>

</head>

<body>
<br><a class="active" href="index.php">Add Device</a></br>
<br><a href="addserver.php">Add Server</a></br>
<br><a href="deletedevice.php">Remove Device</a></br>
<br><a href="deleteserver.php">Remove Server</a></br>
<br><a href="index1.php">Monitor Servers</a></br>
<br><a href="device_interfaces.php">Monitor Devices</a></br>
<br><a href="dev-server.php">Monitor Both</a></br>

 <h3> Select the server to be removed </h3>
 
 
 <?php 
 
 #require "find.php";
$myfile = fopen("../db.conf", "r") or die("Unable to open file!");
eval(fread($myfile,filesize("../db.conf")));
fclose($myfile);

$conn = mysqli_connect($host,$username, $password,$database,$port);
// Checkconnection
if (!$conn) {
   die("Connection failed: " . mysqli_connect_error());
}
#echo "Connected successfully<br>";
mysqli_select_db($conn,"$database");
if (!empty($_POST['delete_list2'])) 
{
foreach($_POST['delete_list2'] as $value)
										 {
														echo "$value<br>"; 
														$sql = "DELETE FROM mani_SERVERS WHERE id=$value";

														if (mysqli_query($conn, $sql)) {
																echo "Record deleted successfully";
														} else {
																echo "Error deleting record: " . mysqli_error($conn);
														}
												
										}


}
 echo "<form action='' method=post>";
 $result = mysqli_query($conn,"SELECT id,server FROM mani_SERVERS");
 echo "<table>";
 echo "<tr><th>Select</th><th>Server</th></tr>";
 
 while($row = mysqli_fetch_array($result))
 {
 echo "<tr><td><input type='checkbox' name='delete_list2[]' value=$row[id]></td><td>$row[server]</td></tr>";
 
 }
 
  echo "</table>";
 echo "<input type=submit value='delete server'>";
 echo "</form>";


 ?>
