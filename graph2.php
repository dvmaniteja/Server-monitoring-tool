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


</form>
</body>
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
                 $username= $data[1];
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

#$now = time();

$_SESSION['check_list2']=$_GET['check_list2'];
$duration=$_SESSION['check_list2'];

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

$metrics1=$_SESSION['check_list1'];

$i=0;
$metrics_info=array();
foreach($metrics1 as $metric){
			$metrics_info[]=$metric;
			$i++;
}
#print_r($metrics_info);

$list=$_SESSION['check_list'];
#echo $list;
#print_r($list);
#$now = time();
echo "<center><h1 style='font-family:Trebuchet MS;font-size:250%'>GRAPH</h1></center>";

if($i==1) {

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

							
							$result = mysqli_query($link10,"SELECT SELECTED_INTERFACES FROM mani_DEVICES_INTERFACES_SELECTED where IP='$y' and PORT='$z' and COMMUNITY='$a'");

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

							
							$result = mysqli_query($link10,"SELECT SELECTED_INTERFACES FROM mani_DEVICES_INTERFACES_SELECTED where IP='$y' and PORT='$z' and COMMUNITY='$a'");

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

							
							$result = mysqli_query($link10,"SELECT SELECTED_INTERFACES FROM mani_DEVICES_INTERFACES_SELECTED where IP='$y' and PORT='$z' and COMMUNITY='$a'");

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

							
							$result = mysqli_query($link10,"SELECT SELECTED_INTERFACES FROM mani_DEVICES_INTERFACES_SELECTED where IP='$y' and PORT='$z' and COMMUNITY='$a'");


											#option for aggregate bit rate of all interfaces

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

											#end of option for aggregate bit rate of all interfaces


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

*/											#end of option	

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

							
							$result = mysqli_query($link10,"SELECT SELECTED_INTERFACES FROM mani_DEVICES_INTERFACES_SELECTED where IP='$y' and PORT='$z' and COMMUNITY='$a'");


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
																array_push($rrd,"DEF:intotal=$device:Intotal:AVERAGE","LINE:intotal#$color:Intotal","GPRINT:intotal:AVERAGE:Aggregate Input %6.2lf %SBps\l");
																$color = str_pad( dechex( mt_rand(0,0xFFFFFF) ),6,'0',STR_PAD_LEFT);		                     
																array_push($rrd,"DEF:outtotal=$device:Outtotal:AVERAGE","LINE:outtotal#$color:Outtotal","GPRINT:outtotal:AVERAGE:Aggregate Output %6.2lf %SBps\l");


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



