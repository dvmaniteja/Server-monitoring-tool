<!DOCTYPE html>
<html>

<head>

</head>

<body>
<script type="text/javascript">
checked=false;
function checkedAll (frm1) {var aa= document.getElementById('frm1'); if (checked == false)
{
checked = true
}
else
{
checked = false
}for (var i =0; i < aa.elements.length; i++){ aa.elements[i].checked = checked;}
}
</script>


<br><a class="active" href="index.php">Add Device</a></br>
<br><a href="addserver.php">Add Server</a></br>
<br><a href="deletedevice.php">Remove Device</a></br>
<br><a href="deleteserver.php">Remove Server</a></br>
<br><a href="index1.php">Monitor Servers</a></br>
<br><a href="device_interfaces.php">Monitor Devices</a></br>
<br><a href="dev-server.php">Monitor Both</a></br>


 <h3> Enter Device Details </h3>

<form action="index.php" method="post">
<table>
<tr><td><center>IP:</center></td>        <td><center><input type="text" name="ip" required></center> </td></tr>
<tr><td><center>PORT:</center></td>        <td><center>    <input type="text" name="port" required> </center></td></tr>
<tr><td><center>COMMUNITY:</center></td>        <td><center> <input type="text" name="community" required></center></td></tr>
</table>
<input type="submit" value="Enter">
</form>


<?php

if(!empty($_POST["ip"])) {
 $x= $_POST["ip"]; $y=$_POST["port"]; $z=$_POST["community"]; 

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

$tbl = "CREATE TABLE IF NOT EXISTS mani_DEVICES ( 
                id int(11) NOT NULL AUTO_INCREMENT,
								IP varchar(255) NOT NULL ,
								PORT int NOT NULL,
								COMMUNITY varchar(255) NOT NULL,
								INTERFACES varchar(48000) NOT NULL,
								PRIMARY KEY (id),
								UNIQUE KEY(IP,PORT,COMMUNITY)
                )"; 
$query = mysqli_query($conn, $tbl); 
if ($query === TRUE) {
	#echo "<h3>blockedusers table created OK :) </h3>"; 
} else {
	echo "<h3>Table Not Created </h3>"; 
}
$sqls = "INSERT INTO mani_DEVICES (IP,PORT,COMMUNITY) VALUES (\"$x\", \"$y\", \"$z\")";

if (mysqli_query($conn, $sqls)) {
    echo "\nNew Device Credentials Added for $x-$y-$z<br>\n";
    $a = snmpwalk("$x:$y", "$z", "1.3.6.1.2.1.2.2.1.1"); 
if($a)
{
echo "<form id ='frm1' action='index.php' method='post'>";
foreach ($a as $val) {
    list($b,$c)=explode(" ", $val);
   # echo "$c<br>";
   echo "<input type='checkbox' name='interface[]' value=$x+$y+$z+$c> $c<br>";
}
}
else{echo "\ndevice unreachable\n";}
echo "<input type='checkbox' name='intereface[]' onclick='checkedAll(frm1);'>selectAll<br>";
echo "<input type=submit value='monitor interfaces'>";
echo "</form>";
} else {
 echo "\nerror device already exists\n";
    echo "Error: " . $sqls . "<br>" . mysqli_error($conn);
}
}

if(!empty($_POST["interface"])) 
							{

								#echo "hii\n";
							
									$interfacearray=array();

									foreach($_POST["interface"] as $check2) 
									{
											list($r,$t,$y,$u)=explode("+", $check2);
											#echo "$u";
    									array_push($interfacearray, "$u");
    							}

    $joined= implode(",", $interfacearray);
    echo "\n$joined-$r-$t-$y\n";


$myfile = fopen("../db.conf", "r") or die("Unable to open file!");
eval(fread($myfile,filesize("../db.conf")));
fclose($myfile);


$conn = mysqli_connect($host,$username, $password,$database,$port);

// Check connection
if (!$conn) {
   die("Connection failed: " . mysqli_connect_error());
}

//echo "Connected successfully<br>";
#echo $joined;

mysqli_select_db($conn,"$database");
    $sqlu = "UPDATE mani_DEVICES SET interfaces='$joined' WHERE IP=\"$r\" AND PORT =\"$t\" AND COMMUNITY=\"$y\"";

if (mysqli_query($conn, $sqlu)) {
    echo "\ninterfaces added succesfully\n";
    
} 

else {

    echo "Error: " . $sqlu . "<br>" . mysqli_error($conn);
}
    }

?>


</body>
</html>
