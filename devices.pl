#!/usr/bin/perl

use DBI;
use DBD::mysql;
use Net::SNMP qw(snmp_dispatcher oid_lex_sort);
use RRD::Simple ();
use Data::Dumper qw(Dumper);
use Cwd 'abs_path'; 

$cwd = abs_path(__FILE__);
@find = split('/', $cwd);
splice @find, -2;
push(@find, 'db.conf');
$realpath = join('/', @find);
require "$realpath";

#my $driver = "mysql";
#my $dsn = "DBI:$driver:$database:$host:$port";
#my $dbh = DBI->connect($dsn, $username, $password ) or die $DBI::errstr;

my $dsn = "DBI:mysql:database=$database;host=$host;port=$port";
my $dbh = DBI->connect($dsn,$username,$password);

#$dbh->do("Create database if not exists $database") or die "Could not create the: ".$database." error: ". $dbh->errstr ."\n";
#$dbh->disconnect();

my $sql55="CREATE TABLE IF NOT EXISTS mani_DEVICES_INTERFACES_SELECTED (id int (11) NOT NULL AUTO_INCREMENT,IP tinytext NOT NULL,PORT int (11) NOT NULL,COMMUNITY tinytext NOT NULL, SELECTED_INTERFACES VARCHAR(1500), BOTHS VARCHAR(1000), PRIMARY KEY ( id )) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
my $sth5 = $dbh->prepare( $sql55 );
$sth5->execute;

my $i=0;
my $x=0;
my %devices;
my %details;
my $sth = $dbh->prepare("SELECT IP, PORT, COMMUNITY,interfaces FROM mani_DEVICES");
$sth->execute() or die $DBI::errstr;


while (my @row = $sth->fetchrow_array()) 
{

my @in_oid;
my @out_oid;

my ($ip, $port, $community,$interfaces ) = @row;

	 $devices{"Device-$ip-$port-$community"}{ip}   = $ip;
	 $devices{"Device-$ip-$port-$community"}{port}    = $port;
	 $devices{"Device-$ip-$port-$community"}{community}   = $community;
   

   my @interfaces_new=split(',',$interfaces);

	 my $i=0;

	 foreach(@interfaces_new)
	 {
			 $devices{"Device-$ip-$port-$community"}{interface}{$_}=$_;
			 push(@in_oid,"1.3.6.1.2.1.2.2.1.10.$_");
			 push(@out_oid,"1.3.6.1.2.1.2.2.1.16.$_");
	 }

push(@all_oids,@in_oid,@out_oid);


								my ($session, $error) = Net::SNMP->session(
									 -hostname    =>  $ip,
									 -community   =>  $community,
									 -port        =>  $port,
									 -nonblocking =>  1,
									 -version     =>  'snmpv2c',
								);

				if (!defined($session)) 
				{
					 printf("ERROR: %s.\n", $error);
					next;
				}

				while ((my $h=@all_oids) > 0)
				{
				  	#print "h value=$h\n";
						#print "@oid_all\n";

							my $result_ifType = $session->get_request(
								                  -varbindlist      => [splice @all_oids, 0, 40],
								                  -callback        => [ \&sub_octet, $ip, $port, $community] ,  
								                  );
								               
										if (!defined($result_ifType))
										{
					 						printf "ERROR: Failed to queue get request for host '%s': %s.\n", $session->hostname(), $session->error();
										}

										else {
											print "\nRequests Dispatched\n";
										}
					}

}

snmp_dispatcher();

sub sub_octet
{

my ($session, $ip, $port, $community) = @_;
my @y;
 
   my $result =  $session->var_bind_list();
   
   if (!defined $result)
    {
      printf "ERROR: Get request failed for host '%s': %s.\n", $session->hostname(), $session->error();
      return;
		}

		else
		{
				foreach (oid_lex_sort(keys(%{$session->var_bind_list()})))
				{
      		$devices{"Device-$ip-$port-$community"}{"ifall"}{$_}=$result->{$_};
        }
    }

}

#print Dumper \%devices;

my @keys = keys %devices;

foreach my $p (@keys)
{
		 my $rrd = RRD::Simple->new( file => "$p.rrd" );
		 my @subject =keys % {$devices{$p}{"interface"}} ;
		 my @y;
		 my @add2;
		 my $j=0;
		 my $inagg=0;
		 my $outagg=0;

					 foreach my $q (@subject)
      		 {
				         $y[$j]=$q;
				         $j++;
           }

           my @b=sort (@y);

           if(@b)
           {
						     my @add2;
						     my $file = "$p.rrd";
						     my $rrd = RRD::Simple->new( file => "$file" );

													 if(! -e $file )   
													 {

													 my @add1;

																	foreach (@b)
																	{
																	 push(@add1,("In$_" => "COUNTER"), ("Out$_" => "COUNTER"));
																	}

																		push(@add1,("Intotal" => "COUNTER"), ("Outtotal" => "COUNTER"));
																		
																		$rrd->create($file,"year", @add1);

																		print "\nRRD Created\n";
													 	}
						    
						     
														foreach my $z (@b)
														{
																if(($devices{"$p"}{"ifall"}{"1.3.6.1.2.1.2.2.1.10.$z"})==noSuchInstance)
																{
																($devices{"$p"}{"ifall"}{"1.3.6.1.2.1.2.2.1.10.$z"})=0;
																}

																if(($devices{"$p"}{"ifall"}{"1.3.6.1.2.1.2.2.1.16.$z"})==noSuchInstance)
																{
																($devices{"$p"}{"ifall"}{"1.3.6.1.2.1.2.2.1.16.$z"})=0;
																}
														
																push(@add2,("In$z" => $devices{"$p"}{"ifall"}{"1.3.6.1.2.1.2.2.1.10.$z"}), ("Out$z" => $devices{"$p"}{"ifall"}{"1.3.6.1.2.1.2.2.1.16.$z"}));
																$inagg=$inagg+$devices{"$p"}{"ifall"}{"1.3.6.1.2.1.2.2.1.10.$z"};
																$outagg=$outagg+$devices{"$p"}{"ifall"}{"1.3.6.1.2.1.2.2.1.16.$z"};
														}

														$devices{"$p"}{"inall"}="$inagg";
														$devices{"$p"}{"outall"}="$outagg";

							print "\n@add2\n";							
							push(@add2,("Intotal" => $devices{"$p"}{"inall"}), ("Outtotal" => $devices{"$p"}{"outall"}));

							my $x1=time();

							$rrd->update("$p.rrd",$x1,@add2);	
							print "\nRRD Updated\n";	
					}
}
