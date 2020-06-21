#!/usr/bin/perl

use CGI;
use Encode;

#-----------------------------------------------------------------------------------
# configuration
#-----------------------------------------------------------------------------------

# max. number of files
$maxFiles = 10;

# path to temporary directory
$tmpDir = '../tmp';

# path to event handler
$evtHandler = '../action.php';

#-----------------------------------------------------------------------------------
# main
#-----------------------------------------------------------------------------------

$cont = $sid = '';

# get container ID and session ID from query string
@query = split('&', $ENV{'QUERY_STRING'});
foreach(@query) {
	($key, $val) = split('=', $_);
	if($key eq 'cont') { $cont = $val; }
	elsif($key eq 'sid') { $sid = $val; }
	elsif($key eq 'tmp') { $tmpDir = $val; }
}
if(!$cont) { print "Content-Type: text/plain\n\nERROR: no container ID"; exit; }
if(!$sid) { print "Content-Type: text/plain\n\nERROR: no session ID"; exit; }

# set data directories
$dataDir = $tmpDir . '/upload/' . $sid;
$uplDir = $dataDir . '/files';

# set counter filename
$monCounter = $dataDir . '/counter.txt';

if(-e $uplDir) {
	# delete uploaded files
	opendir(DIR, $uplDir);
	@files = readdir(DIR);
	closedir(DIR);
	foreach(@files) { unlink($uplDir . '/' . $_); }
	# delete counter file
	unlink($dataDir . '/counter.txt');
}
else {
	# create directories
	if(!(-e $dataDir)) { mkdir($dataDir); }
	mkdir($uplDir);
}

# get total size of uploaded POST data and set start time
$bytesTotal = $ENV{'CONTENT_LENGTH'};
$timeStart = time;

$bytesFile = $bytesSum = $cnt = 0;
$prevFilename = '';
$cgi = CGI->new(\&upl_hook);

# save file(s) in upload directory
for($i = 0; $i < $maxFiles; $i++) {
	$filename = $cgi->param("fmFile[$i]");
	$filename =~ s/\///g;
	if($filename ne '') {
		$fileHandle = $cgi->upload("fmFile[$i]");
		open(FILE, '>' . $uplDir . '/' . $filename);
		binmode FILE;
		while(<$fileHandle>) { print FILE; }
		close(FILE);
		close($fileHandle);
	}
}

# redirect to event handler
$replSpaces = $cgi->param('fmReplSpaces');
$lowerCase = $cgi->param('fmLowerCase');
print "Location: $evtHandler?fmContainer=$cont&fmMode=upload&fmReplSpaces=$replSpaces&fmLowerCase=$lowerCase\n\n";
1;

#-----------------------------------------------------------------------------------
# subs
#-----------------------------------------------------------------------------------

sub upl_hook {
	my ($filename, $buffer, $bytesRead, $data) = @_;

	if($bytesFile < $bytesRead) {
		$bytesFile = $bytesRead;
	}

	if($filename ne $prevFilename) {
		$prevFilename = $filename;
		$bytesSum += $bytesFile;
		$bytesFile = 0;
	}
	my $bytesCurrent = $bytesSum + $bytesRead;
	if($bytesCurrent <= $bytesTotal) {
		$filename =~ s/"/\"/g;
		open(MON, '>' . $monCounter);
		print MON '{filename:"' . encode('UTF-8', $filename) . '"';
		print MON ',bytesTotal:' . sprintf('%d', $bytesTotal);
		print MON ',bytesCurrent:' . sprintf('%d', $bytesCurrent);
		print MON ',timeStart:' . sprintf('%d', $timeStart);
		print MON ',timeCurrent:' . time;
		print MON ',cnt:' . ++$cnt . '}';
		close(MON);
	}
}
