
Software Requirements:
----------------------

1. Operating System: Ubuntu 14.04 LTS.

2. You need to install Apache server, MySQL and PHP.

3. Modules which are needed to be installed from CPAN are:
	 Data::Dumper
	 DBD::Mysql
	 DBI
   FindBin
   File::Basename
   File::Spec::Functions
	 LWP::Simple
	 LWP::UserAgent
   Net::SNMP
   Net::SNMP::Interfaces
	 RRDs
 
Steps to run this assignment:
-----------------------------
1. Go to the terminal in move to the directory where this folder is present, say, /var/www/html/et2536-vedb15/assignment2/ 
   (It is assumed that the working directory configured in the apache server is /var/www/html/, change the path accordingly) 

2. Run the shell script "backend" in the terminal with the command "perl backend".

3. Now, open a web browser and type the following URL: (It is assummed that the folder is in /var/www/html/.
	 http://localhost/et2536-vedb15/assignment2/index.php

4. Choose the desired options to view the server and device metrics.


