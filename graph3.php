<html>
<br><a class="active" href="index.php">Add Device</a><br>
<br><a href="addserver.php">Add Server</a><br>
<br><a href="deletedevice.php">Remove Device</a><br>
<br><a href="deleteserver.php">Remove Server</a><br>
<br><a href="index1.php">Monitor Servers</a><br>
<br><a href="device_interfaces.php">Monitor Devices</a><br>
<br><a href="index7.php">Monitor Both</a><br>
</html>

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

$_SESSION['check_list5']=$_GET['check_list5'];
$duration=$_SESSION['check_list5'];

if ($duration[0]  == "-1h")
{
$title="Hourly";
}
elseif ($duration[0]  == "-1d")
{
$title="Daily";
}
elseif ($duration[0]  == "-1w")
{
$title="Weekly";
}
elseif ($duration[0]  == "-1m")
{
$title="Monthly";
}
elseif ($duration[0]  == "-1y")
{
$title="Yearly";
}

#echo $duration[0];

$servers_list=$_SESSION['check_list1'];

$server=array();
$de=0;
foreach($servers_list as $serr){
			#echo $metric."</br>";
			$sernm="Server-".$serr.".rrd";
			$server[]=$sernm;
			$de++;
		}

$metrics=$_SESSION['check_list2']; #server metrics count

$i=0;
foreach($metrics as $metric){
			#echo $metric."</br>";
			$i++;
		}

$list=$_SESSION['check_list3'];


#echo $i;
echo "<center><h1 style='font-family:Trebuchet MS;font-size:250%'>GRAPH</h1></center>";

#code below to show one graph per device/ one graph per server, with all metrics in one graph for that server

	if($i==1){
					$k=0;
					#echo $metrics[0];

					foreach($server as $server_graph) {
							$value1="val1".$k;
							$name1=$title.$k.".png";

							$parts = split ("\.", $server_graph);
							$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
							#echo "$ip"; 

							if($metrics[0]=="cpuusage") {
								$grh1='S%%';
							}
							elseif($metrics[0]=="bytespersec") {
								$grh1='SBps';
							}
							elseif($metrics[0]=="bytesperreq") {
								$grh1='SB';
							}
							elseif($metrics[0]=="reqpersec") {
								$grh1='Srps';
							}

							$value1 =	array( "--start", "$duration[0]",
																		"DEF:bps1=$server_graph:$metrics[0]:AVERAGE",
																		"LINE2:bps1#0000FF:$metrics[0]",
																		"--dynamic-labels","--title=$title graph for $ip",
																		"--color=BACK#CCCCCC","--color=CANVAS#CCFFFF",    
																		"--color=SHADEB#9999CC",
																		"COMMENT:\\n",
																		"GPRINT:bps1:AVERAGE:Average $metrics[0] \: %6.2lf %$grh1",
										 			);	
				
																		$ret_f1 = rrd_graph($name1, $value1);

																		if( $ret_f1 == 0 )
																		{
																				$err = rrd_error();
																				echo "Create error: $err\n";
																		}

							echo "<img src=image.php?value=$name1 style='float: left; margin-right: 1%;'>";
							$k++;
					}
	}

elseif($i==2){
		#echo $metrics[0];
		#echo $metrics[1];
		#echo $metrics[2];
		#echo $metrics[3];

		$k=0;

		foreach($server as $server_graph) {
							
							$value1="val1".$k;
							$name1=$title.$k.".png";

							$parts = split ("\.", $server_graph);
							$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
							#echo "$ip"; 

if($metrics[0]=="cpuusage") {
		$grh1='S%%';
		}
		elseif($metrics[0]=="bytespersec") {
		$grh1='SBps';
		}
		elseif($metrics[0]=="bytesperreq") {
		$grh1='SB';
		}
		elseif($metrics[0]=="reqpersec") {
		$grh1='Srps';
		}

		if($metrics[1]=="cpuusage") {
		$grh2='S%%';
		}
		elseif($metrics[1]=="bytespersec") {
		$grh2='SBps';
		}
		elseif($metrics[1]=="bytesperreq") {
		$grh2='SB';
		}
		elseif($metrics[1]=="reqpersec") {
		$grh2='Srps';
		}

		$value1 = array( "--start", "$duration[0]",
													"DEF:bps1=$server_graph:$metrics[0]:AVERAGE",
													"DEF:bps2=$server_graph:$metrics[1]:AVERAGE",
													#"DEF:bps3=$server_graph:$metrics[2]:AVERAGE",
													#"DEF:bps4=$server_graph:$metrics[3]:AVERAGE",
													"LINE2:bps1#0000FF:$metrics[0]",
													"LINE2:bps2#00FF00:$metrics[1]",
													#"LINE2:bps3#FF9999:$metrics[2]",
													#"LINE2:bps4#FFFF00:$metrics[3]",
													"--dynamic-labels","--title=$title graph for $ip",
	  											"--color=BACK#CCCCCC","--color=CANVAS#CCFFFF",    
		    	  							"--color=SHADEB#9999CC",
													"COMMENT:\\n",
													"GPRINT:bps1:AVERAGE:Average $metrics[0] \: %6.2lf %$grh1",
													"COMMENT:\\n",
													"GPRINT:bps2:AVERAGE:Average $metrics[1] \: %6.2lf %$grh2",
													#"COMMENT:\\n",
													#"GPRINT:bps3:AVERAGE:Average $metrics[2] \: %6.2lf %S",
													#"COMMENT:\\n",
													#"GPRINT:bps4:AVERAGE:Average $metrics[3] \: %6.2lf %S",
													
		       			);	
				
													$ret_f1 = rrd_graph($name1, $value1);

													if( $ret_f1 == 0 )
													{
															$err = rrd_error();
															echo "Create error: $err\n";
													}

							echo "<img src=image.php?value=$name1 style='float: left; margin-right: 1%;'>";
							$k++;

					}
	}

elseif($i==3){
		#echo $metrics[0];
		#echo $metrics[1];
		#echo $metrics[2];
		#echo $metrics[3];

		$k=0;

		foreach($server as $server_graph) {
							
							$value1="val1".$k;
							$name1=$title.$k.".png";

							$parts = split ("\.", $server_graph);
							$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
							#echo "$ip"; 

	if($metrics[0]=="cpuusage") {
		$grh1='S%%';
		}
		elseif($metrics[0]=="bytespersec") {
		$grh1='SBps';
		}
		elseif($metrics[0]=="bytesperreq") {
		$grh1='SB';
		}
		elseif($metrics[0]=="reqpersec") {
		$grh1='Srps';
		}

		if($metrics[1]=="cpuusage") {
		$grh2='S%%';
		}
		elseif($metrics[1]=="bytespersec") {
		$grh2='SBps';
		}
		elseif($metrics[1]=="bytesperreq") {
		$grh2='SB';
		}
		elseif($metrics[1]=="reqpersec") {
		$grh2='Srps';
		}

		if($metrics[2]=="cpuusage") {
		$grh3='S%%';
		}
		elseif($metrics[2]=="bytespersec") {
		$grh3='SBps';
		}
		elseif($metrics[2]=="bytesperreq") {
		$grh3='SB';
		}
		elseif($metrics[2]=="reqpersec") {
		$grh3='Srps';
		}


		$value1 = array( "--start", "$duration[0]",
													"DEF:bps1=$server_graph:$metrics[0]:AVERAGE",
													"DEF:bps2=$server_graph:$metrics[1]:AVERAGE",
													"DEF:bps3=$server_graph:$metrics[2]:AVERAGE",
													#"DEF:bps4=$server_graph:$metrics[3]:AVERAGE",
													"LINE2:bps1#0000FF:$metrics[0]",
													"LINE2:bps2#00FF00:$metrics[1]",
													"LINE2:bps3#FF9999:$metrics[2]",
													#"LINE2:bps4#FFFF00:$metrics[3]",
													"--dynamic-labels","--title=$title graph for $ip",
	  											"--color=BACK#CCCCCC","--color=CANVAS#CCFFFF",    
		    	  							"--color=SHADEB#9999CC",
													"COMMENT:\\n",
													"GPRINT:bps1:AVERAGE:Average $metrics[0] \: %6.2lf %$grh1",
													"COMMENT:\\n",
													"GPRINT:bps2:AVERAGE:Average $metrics[1] \: %6.2lf %$grh2",
													"COMMENT:\\n",
													"GPRINT:bps3:AVERAGE:Average $metrics[2] \: %6.2lf %$grh3",
													#"COMMENT:\\n",
													#"GPRINT:bps4:AVERAGE:Average $metrics[3] \: %6.2lf %S",
													
		       			);	
				
													$ret_f1 = rrd_graph($name1, $value1);

													if( $ret_f1 == 0 )
													{
															$err = rrd_error();
															echo "Create error: $err\n";
													}

							echo "<img src=image.php?value=$name1 style='float: left; margin-right: 1%;'>";


							$k++;

					}
	}

	elseif($i==4){
		#echo $metrics[0];
		#echo $metrics[1];
		#echo $metrics[2];
		#echo $metrics[3];

		$k=0;

		foreach($server as $server_graph) {
							
							$value1="val1".$k;
							$name1=$title.$k.".png";

							$parts = split ("\.", $server_graph);
							$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
							#echo "$ip"; 

if($metrics[0]=="cpuusage") {
		$grh1='S%%';
		}
		elseif($metrics[0]=="bytespersec") {
		$grh1='SBps';
		}
		elseif($metrics[0]=="bytesperreq") {
		$grh1='SB';
		}
		elseif($metrics[0]=="reqpersec") {
		$grh1='Srps';
		}

		if($metrics[1]=="cpuusage") {
		$grh2='S%%';
		}
		elseif($metrics[1]=="bytespersec") {
		$grh2='SBps';
		}
		elseif($metrics[1]=="bytesperreq") {
		$grh2='SB';
		}
		elseif($metrics[1]=="reqpersec") {
		$grh2='Srps';
		}

		if($metrics[2]=="cpuusage") {
		$grh3='S%%';
		}
		elseif($metrics[2]=="bytespersec") {
		$grh3='SBps';
		}
		elseif($metrics[2]=="bytesperreq") {
		$grh3='SB';
		}
		elseif($metrics[2]=="reqpersec") {
		$grh3='Srps';
		}

		if($metrics[3]=="cpuusage") {
		$grh4='S%%';
		}
		elseif($metrics[3]=="bytespersec") {
		$grh4='SBps';
		}
		elseif($metrics[3]=="bytesperreq") {
		$grh4='SB';
		}
		elseif($metrics[3]=="reqpersec") {
		$grh4='Srps';
		}

		$value1 = array( "--start", "$duration[0]",
													"DEF:bps1=$server_graph:$metrics[0]:AVERAGE",
													"DEF:bps2=$server_graph:$metrics[1]:AVERAGE",
													"DEF:bps3=$server_graph:$metrics[2]:AVERAGE",
													"DEF:bps4=$server_graph:$metrics[3]:AVERAGE",
													"LINE2:bps1#0000FF:$metrics[0]",
													"LINE2:bps2#00FF00:$metrics[1]",
													"LINE2:bps3#FF9999:$metrics[2]",
													"LINE2:bps4#FFFF00:$metrics[3]",
													"--dynamic-labels","--title=$title graph for $ip",
	  											"--color=BACK#CCCCCC","--color=CANVAS#CCFFFF",    
		    	  							"--color=SHADEB#9999CC",
													"COMMENT:\\n",
													"GPRINT:bps1:AVERAGE:Average $metrics[0] \: %6.2lf %$grh1",
													"COMMENT:\\n",
													"GPRINT:bps2:AVERAGE:Average $metrics[1] \: %6.2lf %$grh2",
													"COMMENT:\\n",
													"GPRINT:bps3:AVERAGE:Average $metrics[2] \: %6.2lf %$grh3",
													"COMMENT:\\n",
													"GPRINT:bps4:AVERAGE:Average $metrics[3] \: %6.2lf %$grh4",
													
		       			);	
				
													$ret_f1 = rrd_graph($name1, $value1);

													if( $ret_f1 == 0 )
													{
															$err = rrd_error();
															echo "Create error: $err\n";
													}

							echo "<img src=image.php?value=$name1 style='float: left; margin-right: 1%;'>";

							$k++;

					}
	}


#code below o show different server metrics in different graphs
/*
if ($i==1) {
$rrd = array("--slope-mode","--title=Server Graph ",
             "--start","$duration[0]",
            );
$n=1;

																foreach($server as $devx){

																$parts = split ("\.", $devx);
																$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
																$name1=$ip.$n.".png";
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																										 array_push($rrd,"DEF:def".$n."=$devx:$metrics[0]:AVERAGE",
																										 "LINE:def".$n."#$color:$metrics[0],$ip","GPRINT:def".$n.":AVERAGE:AVG %6.2lf\l");
																$n++;
																}

																$dth = rrd_graph("$name1",$rrd);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
																}


																echo "<img src=image.php?value=$name1 style='float: left; margin-right: 1%;'>";

}


if ($i==2) {

				$rrd = array("--slope-mode","--title=Server Graph ",
             "--start","$duration[0]",
            );
$n=1;

																foreach($server as $devx){

																$parts = split ("\.", $devx);
																$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
																$name1=$ip.$n.".png";
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																										 array_push($rrd,"DEF:def".$n."=$devx:$metrics[0]:AVERAGE",
																										 "LINE:def".$n."#$color:$metrics[0],$ip","GPRINT:def".$n.":AVERAGE:AVG %6.2lf\l");
																$n++;
																}

																$dth = rrd_graph("$name1",$rrd);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
																}


																echo "<img src=image.php?value=$name1 style='float: left; margin-right: 1%;'>";

$rrd1 = array("--slope-mode","--title=Server Graph ",
             "--start","$duration[0]",
            );
$k=1;

																foreach($server as $devx){

																$parts = split ("\.", $devx);
																$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
																$name2="a".$ip.$k.".png";
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																										 array_push($rrd1,"DEF:def".$k."=$devx:$metrics[1]:AVERAGE",
																										 "LINE:def".$k."#$color:$metrics[1],$ip","GPRINT:def".$k.":AVERAGE:AVG %6.2lf\l");
																$k++;
																}

																$dth = rrd_graph("$name2",$rrd1);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
																}


																echo "<img src=image.php?value=$name2 style='float: left; margin-right: 1%;'>";


}

if ($i==3) {

				$rrd = array("--slope-mode","--title=Server Graph ",
             "--start","$duration[0]",
            );
$n=1;

																foreach($server as $devx){

																$parts = split ("\.", $devx);
																$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
																$name1=$ip.$n.".png";
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																										 array_push($rrd,"DEF:def".$n."=$devx:$metrics[0]:AVERAGE",
																										 "LINE:def".$n."#$color:$metrics[0],$ip","GPRINT:def".$n.":AVERAGE:AVG %6.2lf\l");
																$n++;
																}

																$dth = rrd_graph("$name1",$rrd);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
																}


																echo "<img src=image.php?value=$name1 style='float: left; margin-right: 1%;'>";

$rrd1 = array("--slope-mode","--title=Server Graph ",
             "--start","$duration[0]",
            );
$k=1;

																foreach($server as $devx){

																$parts = split ("\.", $devx);
																$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
																$name2="a".$ip.$k.".png";
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																										 array_push($rrd1,"DEF:def".$k."=$devx:$metrics[1]:AVERAGE",
																										 "LINE:def".$k."#$color:$metrics[1],$ip","GPRINT:def".$k.":AVERAGE:AVG %6.2lf\l");
																$k++;
																}

																$dth = rrd_graph("$name2",$rrd1);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
																}


																echo "<img src=image.php?value=$name2 style='float: left; margin-right: 1%;'>";

$rrd2 = array("--slope-mode","--title=Server Graph ",
             "--start","$duration[0]",
            );
$y=1;

																foreach($server as $devx){

																$parts = split ("\.", $devx);
																$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
																$name3="b".$ip.$y.".png";
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																										 array_push($rrd2,"DEF:def".$y."=$devx:$metrics[2]:AVERAGE",
																										 "LINE:def".$y."#$color:$metrics[2],$ip","GPRINT:def".$y.":AVERAGE:AVG %6.2lf\l");
																$y++;
																}

																$dth = rrd_graph("$name3",$rrd2);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
																}


																echo "<img src=image.php?value=$name3 style='float: left; margin-right: 1%;'>";


}

if ($i==4) {

				$rrd = array("--slope-mode","--title=Server Graph ",
             "--start","$duration[0]",
            );
$n=1;

																foreach($server as $devx){

																$parts = split ("\.", $devx);
																$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
																$name1=$ip.$n.".png";
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																										 array_push($rrd,"DEF:def".$n."=$devx:$metrics[0]:AVERAGE",
																										 "LINE:def".$n."#$color:$metrics[0],$ip","GPRINT:def".$n.":AVERAGE:AVG %6.2lf\l");
																$n++;
																}

																$dth = rrd_graph("$name1",$rrd);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
																}


																echo "<img src=image.php?value=$name1 style='float: left; margin-right: 1%;'>";

$rrd1 = array("--slope-mode","--title=Server Graph ",
             "--start","$duration[0]",
            );
$k=1;

																foreach($server as $devx){

																$parts = split ("\.", $devx);
																$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
																$name2="a".$ip.$k.".png";
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																										 array_push($rrd1,"DEF:def".$k."=$devx:$metrics[1]:AVERAGE",
																										 "LINE:def".$k."#$color:$metrics[1],$ip","GPRINT:def".$k.":AVERAGE:AVG %6.2lf\l");
																$k++;
																}

																$dth = rrd_graph("$name2",$rrd1);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
																}


																echo "<img src=image.php?value=$name2 style='float: left; margin-right: 1%;'>";

$rrd2 = array("--slope-mode","--title=Server Graph ",
             "--start","$duration[0]",
            );
$y=1;

																foreach($server as $devx){

																$parts = split ("\.", $devx);
																$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
																$name3="b".$ip.$y.".png";
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																										 array_push($rrd2,"DEF:def".$y."=$devx:$metrics[2]:AVERAGE",
																										 "LINE:def".$y."#$color:$metrics[2],$ip","GPRINT:def".$y.":AVERAGE:AVG %6.2lf\l");
																$y++;
																}

																$dth = rrd_graph("$name3",$rrd2);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
																}


																echo "<img src=image.php?value=$name3 style='float: left; margin-right: 1%;'>";

$rrd3 = array("--slope-mode","--title=Server Graph ",
             "--start","$duration[0]",
            );
$e=1;

																foreach($server as $devx){

																$parts = split ("\.", $devx);
																$ip = $parts[0].".".$parts[1].".".$parts[2].".".$parts[3];
																$name4="b".$ip.$y.".png";
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																										 array_push($rrd3,"DEF:def".$e."=$devx:$metrics[3]:AVERAGE",
																										 "LINE:def".$e."#$color:$metrics[3],$ip","GPRINT:def".$e.":AVERAGE:AVG %6.2lf\l");
																$e++;
																}

																$dth = rrd_graph("$name4",$rrd3);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
																}

																echo "<img src=image.php?value=$name4 style='float: left; margin-right: 1%;'>";

}

*/

$list=$_SESSION['check_list3'];

#print_r($list);

$metrics_dev=$_SESSION['check_list4'];

$demi=0;
$metrics_info=array();
foreach($metrics_dev as $metric){
			$metrics_info[]=$metric;
			$demi++;
}

#echo $metrics_info[0];
if($demi==1) {

		if($metrics_info[0] == "inout") {
						#echo "hi";
						$xvc=0;

							foreach($list as $inter){
										#echo $inter."</br>";
										$devices = split ("\-", $inter);
										$x=$devices[0];
										$y=$devices[1];
										$z=$devices[2];
										$a=$devices[3];

										$device=$x."-".$y."-".$z."-".$a.".rrd";
										#echo $device;
										$img=$y."-".$z."-".$a.".png";

										$name0='$item1'.$xvc;
										#echo $name0;
										$name0=array();

										$name5='$images1'.$xvc;
										#echo $name1;
										$name5=array();

							
							$result = mysqli_query($link10,"SELECT BOTHS FROM mani_DEVICES_INTERFACES_SELECTED where IP='$y' and PORT='$z' and COMMUNITY='$a'");

											while($row = mysqli_fetch_array($result))
											{
											#echo $row;
													$devices_name=$x."-".$y."-".$z."-".$a.".rrd";
												#	echo $devices_name;
													$parts = preg_split('/,/', $row[0]);

													#echo count($parts);

															$ID=array();

															for($i=0;$i<count($parts);$i++)
															{
																			if($i==0)
																			{
																			$c="input"."$parts[$i]";
																			}
																			else
																			{
																			$c="input"."$parts[$i]".",+";
																			}
																			array_push($ID,$c);
															}

															#print_r($ID);

															$CDEF=join(',',$ID);
															$OD=array();

															for($i=0;$i<count($parts);$i++)
															{
																		if($i==0)
																		{
																		$c="output"."$parts[$i]";
																		}
																		else
																		{
																		$c="output"."$parts[$i]".",+";
																		}
																		array_push($OD,$c);
															}

															#print_r($OD);

															$CD=join(',',$OD);

																$rrd = array("--slope-mode","--title=$title Graph for $y-$z-$a",
																						"--start","$duration[0]",
																						#"--end",$now
																						);

																$baseCol=16;
																$loopC=0;

																foreach($parts as $m){
																		   $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   array_push($rrd,"DEF:input$m=$device:In$m:AVERAGE","LINE:input$m#$color:INPUT$m","GPRINT:input$m:AVERAGE:AVERAGE %6.2lf %SBps\l");
																		   $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   array_push($rrd,"DEF:output$m=$device:Out$m:AVERAGE","LINE:output$m#$color:OUTPUT$m","GPRINT:output$m:AVERAGE:AVERAGE %6.2lf %SBps\l");
																}

																  		 #$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		     
																			 #array_push($rrd,"CDEF:Input=$CDEF","LINE:Input#$color:Aggregate Input","GPRINT:Input:AVERAGE: IN AVG %6.2lf\l");
																			# $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																			# array_push($rrd,"CDEF:Output=$CD","LINE:Output#$color:Aggregate Output","GPRINT:Output:AVERAGE: OUT AVG %6.2lf\l");

																#print_r($rrd);
																$loopC=$loopC+2;	
																$dth = rrd_graph($img,$rrd);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
															}

																#echo "<img src=img.png>";

																echo "<img src=image.php?value=$img style='float: left; margin-right: 1%;'>";

														}

											$xvc++;

											}
			#end of inout		
		}


		if($metrics_info[0] == "in") {
						#echo "hi";
						$xvc=0;

							foreach($list as $inter){
										#echo $inter."</br>";
										$devices = split ("\-", $inter);
										$x=$devices[0];
										$y=$devices[1];
										$z=$devices[2];
										$a=$devices[3];

										$device=$x."-".$y."-".$z."-".$a.".rrd";
										$img=$y."-".$z."-".$a.".png";

										$name0='$item1'.$xvc;
										#echo $name0;
										$name0=array();

										$name5='$images1'.$xvc;
										#echo $name1;
										$name5=array();

							
							$result = mysqli_query($link10,"SELECT BOTHS FROM mani_DEVICES_INTERFACES_SELECTED where IP='$y' and PORT='$z' and COMMUNITY='$a'");

											while($row = mysqli_fetch_array($result))
											{
											#echo $row;
													$devices_name=$x."-".$y."-".$z."-".$a.".rrd";
													#echo $devices_name;
													$parts = preg_split('/,/', $row[0]);

													#echo count($parts);

															$ID=array();

															for($i=0;$i<count($parts);$i++)
															{
																			if($i==0)
																			{
																			$c="input"."$parts[$i]";
																			}
																			else
																			{
																			$c="input"."$parts[$i]".",+";
																			}
																			array_push($ID,$c);
															}

															#print_r($ID);

															$CDEF=join(',',$ID);
															$OD=array();

															for($i=0;$i<count($parts);$i++)
															{
																		if($i==0)
																		{
																		$c="output"."$parts[$i]";
																		}
																		else
																		{
																		$c="output"."$parts[$i]".",+";
																		}
																		array_push($OD,$c);
															}

															#print_r($OD);

															$CD=join(',',$OD);

																$rrd = array("--slope-mode","--title=$title Graph for $y-$z-$a",
																						"--start","$duration[0]",
																						#"--end",$now
																						);

																$baseCol=16;
																$loopC=0;

																foreach($parts as $m){
																		   $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   array_push($rrd,"DEF:input$m=$device:In$m:AVERAGE","LINE:input$m#$color:INPUT$m","GPRINT:input$m:AVERAGE:AVERAGE %6.2lf %SBps\l");
																		   #$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   #array_push($rrd,"DEF:output$m=$device:Output$m:AVERAGE","LINE:output$m#$color:OUTPUT$m","GPRINT:output$m:AVERAGE:OUT AVG %6.2lf\l");
																}

																#$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		     
																#array_push($rrd,"CDEF:Input=$CDEF","LINE:Input#$color:Aggregate Input","GPRINT:Input:AVERAGE: IN AVG %6.2lf\l");
																#print_r($rrd);
																$loopC=$loopC+2;	
																$dth = rrd_graph($img,$rrd);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
															}

																#echo "<img src=img.png>";

																echo "<img src=image.php?value=$img style='float: left; margin-right: 1%;'>";

														}

											$xvc++;

											}
		#end of in	
		}

		if($metrics_info[0] == "out") {
						#echo "hi";
						$xvc=0;

							foreach($list as $inter){
										#echo $inter."</br>";
										$devices = split ("\-", $inter);
										$x=$devices[0];
										$y=$devices[1];
										$z=$devices[2];
										$a=$devices[3];

										$device=$x."-".$y."-".$z."-".$a.".rrd";
										$img=$y."-".$z."-".$a.".png";

										$name0='$item1'.$xvc;
										#echo $name0;
										$name0=array();

										$name5='$images1'.$xvc;
										#echo $name1;
										$name5=array();

							
							$result = mysqli_query($link10,"SELECT BOTHS FROM mani_DEVICES_INTERFACES_SELECTED where IP='$y' and PORT='$z' and COMMUNITY='$a'");

											while($row = mysqli_fetch_array($result))
											{
											#echo $row;
													$devices_name=$x."-".$y."-".$z."-".$a.".rrd";
													#echo $devices_name;
													$parts = preg_split('/,/', $row[0]);

													#echo count($parts);

															$ID=array();

															for($i=0;$i<count($parts);$i++)
															{
																			if($i==0)
																			{
																			$c="input"."$parts[$i]";
																			}
																			else
																			{
																			$c="input"."$parts[$i]".",+";
																			}
																			array_push($ID,$c);
															}

															#print_r($ID);

															$CDEF=join(',',$ID);
															$OD=array();

															for($i=0;$i<count($parts);$i++)
															{
																		if($i==0)
																		{
																		$c="output"."$parts[$i]";
																		}
																		else
																		{
																		$c="output"."$parts[$i]".",+";
																		}
																		array_push($OD,$c);
															}

															#print_r($OD);

															$CD=join(',',$OD);

																$rrd = array("--slope-mode","--title=$title Graph for $y-$z-$a",
																						"--start","$duration[0]",
																						#"--end",$now
																						);

																$baseCol=16;
																$loopC=0;

																foreach($parts as $m){
																		   #$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   #array_push($rrd,"DEF:input$m=$device:Input$m:AVERAGE","LINE:input$m#$color:INPUT$m","GPRINT:input$m:AVERAGE:IN AVG %6.2lf\l");
																		   $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   array_push($rrd,"DEF:output$m=$device:Out$m:AVERAGE","LINE:output$m#$color:OUTPUT$m","GPRINT:output$m:AVERAGE:AVERAGE %6.2lf %SBps\l");
																}

																 #$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																 #array_push($rrd,"CDEF:Output=$CD","LINE:Output#$color:Aggregate Output","GPRINT:Output:AVERAGE: OUT AVG %6.2lf\l");

																#print_r($rrd);
																$loopC=$loopC+2;	
																$dth = rrd_graph($img,$rrd);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
															}

																#echo "<img src=img.png>";

																echo "<img src=image.php?value=$img style='float: left; margin-right: 1%;'>";

														}

											$xvc++;

											}
		#end of out	
		}

	if($metrics_info[0] == "aggr") {
						#echo "hi";
						$xvc=0;

							foreach($list as $inter){
										#echo $inter."</br>";
										$devices = split ("\-", $inter);
										$x=$devices[0];
										$y=$devices[1];
										$z=$devices[2];
										$a=$devices[3];

										$device=$x."-".$y."-".$z."-".$a.".rrd";
										$img=$y."-".$z."-".$a.".png";

										$name0='$item1'.$xvc;
										#echo $name0;
										$name0=array();

										$name5='$images1'.$xvc;
										#echo $name1;
										$name5=array();

							
							$result = mysqli_query($link10,"SELECT BOTHS FROM mani_DEVICES_INTERFACES_SELECTED where IP='$y' and PORT='$z' and COMMUNITY='$a'");

											#option for aggregate of all interfaces
											while($row = mysqli_fetch_array($result))
											{
											#echo $row;
													$devices_name=$x."-".$y."-".$z."-".$a.".rrd";
													#echo $devices_name;
													#$parts = preg_split('/\s+/', $row[0]);

													#echo count($parts);

												
																$rrd = array("--slope-mode","--title=$title Graph for $x-$y-$z",
																						"--start","$duration[0]",
																						#"--end",$now
																						);

																$baseCol=16;
																$loopC=0;

																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																array_push($rrd,"DEF:intotal=$device:Intotal:AVERAGE","LINE:intotal#$color:Intotal","GPRINT:intotal:AVERAGE:Aggregate Input %6.2lf\l");
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																array_push($rrd,"DEF:outtotal=$device:Outtotal:AVERAGE","LINE:outtotal#$color:Outtotal","GPRINT:outtotal:AVERAGE:Aggregate Output %6.2lf\l");


																#print_r($rrd);
																$loopC=$loopC+2;	
																$dth = rrd_graph($img,$rrd);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
															}

															#print_r($rrd);

																#echo "<img src=img.png>";

																echo "<img src=image.php?value=$img style='float: left; margin-right: 1%;'>";
											}

											#end of option for aggregate of all interfaces


/*
											#option if aggregate of selected interfaces is asked

											while($row = mysqli_fetch_array($result))
											{
											#echo $row;
													$devices_name=$x."-".$y."-".$z."-".$a.".rrd";
													#echo $devices_name;
													$parts = preg_split('/,/', $row[0]);

													#echo count($parts);

													$ID=array();

															for($i=0;$i<count($parts);$i++)
															{
																			if($i==0)
																			{
																			$c="input"."$parts[$i]";
																			}
																			else
																			{
																			$c="input"."$parts[$i]".",+";
																			}
																			array_push($ID,$c);
															}

															#print_r($ID);

															$CDEF=join(',',$ID);
															$OD=array();

															for($i=0;$i<count($parts);$i++)
															{
																		if($i==0)
																		{
																		$c="output"."$parts[$i]";
																		}
																		else
																		{
																		$c="output"."$parts[$i]".",+";
																		}
																		array_push($OD,$c);
															}

															#print_r($OD);

															$CD=join(',',$OD);

																$rrd = array("--slope-mode","--title=$title Graph for $x-$y-$z",
																						"--start","$duration[0]",
																						#"--end",$now
																						);

																$baseCol=16;
																$loopC=0;

															foreach($parts as $m){
																		   $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   array_push($rrd,"DEF:input$m=$device:In$m:AVERAGE");
																		   $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   array_push($rrd,"DEF:output$m=$device:Out$m:AVERAGE");
																}

																 array_push($rrd,"CDEF:Input=$CDEF","LINE:Input#FF00FF:Aggregate Input","GPRINT:Input:AVERAGE: Aggregate Input %6.2lf\l");
																 #$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																 array_push($rrd,"CDEF:Output=$CD","LINE:Output#000FFF:Aggregate Output","GPRINT:Output:AVERAGE: Aggregate Output %6.2lf\l");

																#print_r($rrd);
																$loopC=$loopC+2;	
																$dth = rrd_graph($img,$rrd);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
															}


																echo "<img src=image.php?value=$img style='float: left; margin-right: 1%;'>";
											}

											#end of option	

*/
											$xvc++;

											}
		#	end of aggr
		}

			if($metrics_info[0] == "all") {
						#echo "hi";
						$xvc=0;

							foreach($list as $inter){
										#echo $inter."</br>";
										$devices = split ("\-", $inter);
										$x=$devices[0];
										$y=$devices[1];
										$z=$devices[2];
										$a=$devices[3];

										$device=$x."-".$y."-".$z."-".$a.".rrd";
										$img=$y."-".$z."-".$a.".png";

										$name0='$item1'.$xvc;
										#echo $name0;
										$name0=array();

										$name5='$images1'.$xvc;
										#echo $name1;
										$name5=array();

							
							$result = mysqli_query($link10,"SELECT BOTHS FROM mani_DEVICES_INTERFACES_SELECTED where IP='$y' and PORT='$z' and COMMUNITY='$a'");

											#option for aggregate of all interfaces
											while($row = mysqli_fetch_array($result))
											{
											#echo $row;
													$devices_name=$x."-".$y."-".$z."-".$a.".rrd";
													#echo $devices_name;
													$parts = preg_split('/,/', $row[0]);

																$rrd = array("--slope-mode","--title=$title Graph for $y-$z-$a",
																						"--start","$duration[0]",
																						#"--end",$now
																						);

																$baseCol=16;
																$loopC=0;

																foreach($parts as $m){
																		   $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   array_push($rrd,"DEF:input$m=$device:In$m:AVERAGE","LINE:input$m#$color:INPUT$m","GPRINT:input$m:AVERAGE:AVERAGE %6.2lf %SBps\l");
																		   $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   array_push($rrd,"DEF:output$m=$device:Out$m:AVERAGE","LINE:output$m#$color:OUTPUT$m","GPRINT:output$m:AVERAGE:AVERAGE %6.2lf %SBps\l");
																}

																  		 
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																array_push($rrd,"DEF:intotal=$device:Intotal:AVERAGE","LINE:intotal#$color:Intotal","GPRINT:intotal:AVERAGE:Aggregate Input %6.2lf\l");
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																array_push($rrd,"DEF:outtotal=$device:Outtotal:AVERAGE","LINE:outtotal#$color:Outtotal","GPRINT:outtotal:AVERAGE:Aggregate Output %6.2lf\l");


																#print_r($rrd);
																$loopC=$loopC+2;	
																$dth = rrd_graph($img,$rrd);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
															}

																#echo "<img src=img.png>";

																echo "<img src=image.php?value=$img style='float: left; margin-right: 1%;'>";

														}

														#end of option for aggregate of all interfaces

/*

											#option for aggregate of selected interfaces

											while($row = mysqli_fetch_array($result))
											{
											#echo $row;
													$devices_name=$x."-".$y."-".$z."-".$a.".rrd";
												#	echo $devices_name;
													$parts = preg_split('/,/', $row[0]);

													#echo count($parts);

															$ID=array();

															for($i=0;$i<count($parts);$i++)
															{
																			if($i==0)
																			{
																			$c="input"."$parts[$i]";
																			}
																			else
																			{
																			$c="input"."$parts[$i]".",+";
																			}
																			array_push($ID,$c);
															}

															#print_r($ID);

															$CDEF=join(',',$ID);
															$OD=array();

															for($i=0;$i<count($parts);$i++)
															{
																		if($i==0)
																		{
																		$c="output"."$parts[$i]";
																		}
																		else
																		{
																		$c="output"."$parts[$i]".",+";
																		}
																		array_push($OD,$c);
															}

															#print_r($OD);

															$CD=join(',',$OD);

																$rrd = array("--slope-mode","--title=$title Graph for $y-$z-$a",
																						"--start","$duration[0]",
																						#"--end",$now
																						);

																$baseCol=16;
																$loopC=0;

																foreach($parts as $m){
																		   $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   array_push($rrd,"DEF:input$m=$device:In$m:AVERAGE","LINE:input$m#$color:INPUT$m","GPRINT:input$m:AVERAGE:AVERAGE %6.2lf %SBps\l");
																		   $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																		   array_push($rrd,"DEF:output$m=$device:Out$m:AVERAGE","LINE:output$m#$color:OUTPUT$m","GPRINT:output$m:AVERAGE:AVERAGE %6.2lf %SBps\l");
																}

																 $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																 array_push($rrd,"CDEF:Input=$CDEF","LINE:Input#FF00FF:Aggregate Input","GPRINT:Input:AVERAGE: Aggregate Input %6.2lf\l");
																 $color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);
																 array_push($rrd,"CDEF:Output=$CD","LINE:Output#000FFF:Aggregate Output","GPRINT:Output:AVERAGE: Aggregate Output %6.2lf\l");

																#print_r($rrd);
																$loopC=$loopC+2;	
																$dth = rrd_graph($img,$rrd);

																if( $dth == 0 )
																{
																	$err = rrd_error();
																	echo "Create error: $err\n";
															}

																#echo "<img src=img.png>";

																echo "<img src=image.php?value=$img style='float: left; margin-right: 1%;'>";

														}

														#end of option for aggregate of selected interfaces

*/
											$xvc++;

											}
			#end of all		
		}

}



?>

<!DOCTYPE html>
<html>

<head>
</head>

<body>
<form method="GET">
<br> <br>

</form>
</body>
</html>
