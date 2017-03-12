#!/usr/local/bin/perl

use strict;
use warnings;

use File::fgets;			#Perl modules used
use Data::Dumper;
use DBI;
use DBD::mysql;
use LWP::Simple;
use LWP::UserAgent;
use RRD::Simple();

my $host;				#variables declared for database connections
my $port;
my $database;
my $username;
my $password;
my @row;
my @array;
my @values;
my $session;
my $error;

use FindBin qw($Bin);
use File::Basename qw(dirname);
use File::Spec::Functions qw(catdir);

#print $Bin, "\n";
my $file = dirname($Bin); 
my $file_path=$file."/db.conf";
#print $file_path;

open (FILE, "$file_path") || die "File not found";

my @lines2 = <FILE>;

foreach my $row (@lines2) 
{
	my @data = split /[="";]/,$row;
	#print "@data";

		if($data[0] eq '$host')
    {		
		$host="$data[2]";
    }
		if($data[0] eq '$port')
    {		
		$port="$data[2]";
    }
		if($data[0] eq '$database')
    {		
		$database="$data[2]";
    }
		if($data[0] eq '$username')
    {		
		$username="$data[2]";
    }
		if($data[0] eq '$password')
    {		
		$password="$data[2]";
    }
}

#print "$port";
my $dsn = "DBI:mysql:database=$database;host=$host;port=$port";
 my $dbh = DBI->connect($dsn,$username,$password);

#my $sql11="CREATE TABLE IF NOT EXISTS A2_SERVERS (id int (11) NOT NULL AUTO_INCREMENT,IP tinytext NOT NULL,PORT int (11) NOT NULL, CPU_USAGE VARCHAR(200), BYTESPERREQ VARCHAR(200), BYTESPERSEC VARCHAR(200), REQPERSEC VARCHAR(200), PRIMARY KEY ( id )) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
#my $sth1 = $dbh->prepare( $sql11 );
#$sth1->execute;

my $sql00="SELECT * from mani_SERVERS";
my $sth = $dbh->prepare( $sql00 );
$sth->execute;

while(@row = $sth->fetchrow_array()) {
	
	my @dsnames=();
	my @credentials=();

	
	my $reqpersec=0;
	my $bytespersec=0;
	my $bytesperreq=0;
	my $cpuload=0;
	my $cpuusage=0;
	my $uptime=0;
	#my $COUNT;

	#my($url)="http://".$row[1].":".$row[2]."/server-status?auto";

	my($url)="http://".$row[1]."/server-status?auto";

	#print $url;

	my($server_status)=get($url);
	
	if (! $server_status) {
	print "Can't access $url\nCheck apache configuration\n\n";
	#exit(0);
	}
	
	else {
	
	
#$total_accesses = $1 if ($server_status =~ /Total\ Accesses:\ ([\d|\.]+)/ig)||0;
#$total_kbytes = $1 if ($server_status =~ /Total\ kBytes:\ ([\d|\.]+)/gi);
	$cpuload = $1 if ($server_status =~ /CPULoad:\ ([\d|\.]+)/gi);
	#$uptime = $1 if ($server_status =~ /Uptime:\ ([\d|\.]+)/gi);
	#$cpuusage=($cpuload*$uptime)/100;
	#print $cpuusage;
	#print "\n";

	$reqpersec = $1 if ($server_status =~ /ReqPerSec:\ ([\d|\.]+)/gi);
	$bytespersec = $1 if ($server_status =~ /BytesPerSec:\ ([\d|\.]+)/gi);
	$bytesperreq = $1 if ($server_status =~ /BytesPerReq:\ ([\d|\.]+)/gi);
#$busyworkers = $1 if ($server_status =~ /BusyWorkers:\ ([\d|\.]+)/gi);
#$idleworkers = $1 if ($server_status =~ /IdleWorkers:\ ([\d|\.]+)/gi);
#$totalworkers = $busyworkers + $idleworkers;

	#print "server:$row[1] port:$row[2]\ncpuload:$cpuload\nuptime:$uptime\ncpuusage:$cpuusage\nbytesperreq:$bytesperreq\nbytespersec:$bytespersec\nreqpersec:$reqpersec\n";

	push @credentials,"cpuusage"=>"$cpuload","reqpersec"=>"$reqpersec","bytespersec"=>"$bytespersec","bytesperreq"=>"$bytesperreq";
	push @dsnames,"cpuusage","reqpersec","bytespersec","bytesperreq";

	#print @dsnames;

	#foreach my $name_ds (@dsnames) {
		#print $name_ds;
		#print "\n";
	#}
					#my $sthaa = $dbh->prepare("SELECT * FROM A2_SERVERS WHERE IP='$row[1]' and PORT='$row[2]'");
					#$sthaa->execute();
					#$COUNT= $sthaa->rows;

					#if ($COUNT==0){		
						#my $sqli="INSERT INTO A2_SERVERS(IP,PORT,CPU_USAGE,BYTESPERREQ,BYTESPERSEC,REQPERSEC) values('$row[1]','$row[2]','$cpuusage','$bytesperreq','$bytespersec','$reqpersec')";
						#	my $sth22 = $dbh->prepare( $sqli );
							#$sth22->execute;
					#}
	}

=pod
														my $rrd = RRD::Simple->new( file => "$row[1].$row[2].rrd",
																									cf => [ qw(AVERAGE MAX) ],
																									default_dstype => "GAUGE",
																									on_missing_ds => "add", );

															unless (-e "$row[1].$row[2].rrd"){ 
																												$rrd->create( "$row[1].$row[2].rrd","mrtg",
																												DS=>"GAUGE");

																			foreach my $name_ds (@dsnames) {
															 									 $rrd->add_source("$row[1].$row[2].rrd",
																								 $name_ds => "GAUGE"
																	 							 );
																			}
															}

													$rrd->update("$row[1].$row[2].rrd",@credentials);
=cut
													my $rrd = RRD::Simple->new( file => "Server-$row[1].rrd",
																									cf => [ qw(AVERAGE MAX) ],
																									default_dstype => "GAUGE",
																									on_missing_ds => "add", );

															unless (-e "Server-$row[1].rrd"){ 
																			$rrd->create( "Server-$row[1].rrd","year",
																									   DS=>"GAUGE");

																			foreach my $name_ds (@dsnames) {
															 									 $rrd->add_source("Server-$row[1].rrd",
																								 $name_ds => "GAUGE"
																	 							 );
																			}
															}

													$rrd->update("Server-$row[1].rrd",@credentials);

print "working\n";
	#exit(0);
}

