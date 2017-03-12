<html>

<br><a class="active" href="index.php">Add Device</a><br>
<br><a href="addserver.php">Add Server</a><br>
<br><a href="deletedevice.php">Remove Device</a><br>
<br><a href="deleteserver.php">Remove Server</a><br>
<br><a href="index1.php">Monitor Servers</a><br>
<br><a href="device_interfaces.php">Monitor Devices</a><br>
<br><a href="dev-server.php">Monitor Both</a><br>

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

$_SESSION['check_list2']=$_GET['check_list2'];

$duration=$_SESSION['check_list2'];

#echo $duration[0];

$server=$_SESSION['check_list'];

#print_r($server);

$metrics=$_SESSION['check_list1'];

#print_r($metrics);


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

$i=0;
foreach($metrics as $metric){
			#echo $metric."</br>";
			$i++;
		}
#echo $i;
echo "<center><h1 style='font-family:Trebuchet MS;font-size:250%'>GRAPH</h1></center>";
#echo $server_graph;


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



#Code below without modification for 4 graphs, each graph corresponding to 4 metrics, all servers in each graph, uncomment, if needed for demo

?>

<!DOCTYPE html>
<html>

<head>

</head>

<body>
<form method="GET">


</form>
</body>
</html>
