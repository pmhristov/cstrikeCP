#!/usr/bin/perl

#-----------------------------------------------------------------------------------
# configuration
#-----------------------------------------------------------------------------------

# path to temporary directory
$tmpDir = '../tmp';

#-----------------------------------------------------------------------------------
# main
#-----------------------------------------------------------------------------------

print "Content-Type: text/plain\n\n";

# get session ID from query string
$sid = '';
@query = split('&', $ENV{'QUERY_STRING'});
foreach(@query) {
	($key, $val) = split('=', $_);
	if($key eq 'sid') { $sid = $val; }
	elsif($key eq 'tmp') { $tmpDir = $val; }
}
if(!$sid) { print 'ERROR: no session ID'; exit; }

# set data directory
$dataDir = $tmpDir . '/upload/' . $sid;

# set counter filename
$monCounter = $dataDir . '/counter.txt';

if(-e $monCounter) {
	# read the monitor file
	open(MON, $monCounter);
	print <MON>;
	close(MON);
}
else {
	# create default JSON string
	print '{filename:"&nbsp;",bytesTotal:0,bytesCurrent:0,timeStart:0,timeCurrent:0,cnt:0}';
}
1;
