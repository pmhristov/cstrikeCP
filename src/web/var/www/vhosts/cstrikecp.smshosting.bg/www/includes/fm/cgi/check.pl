#!/usr/bin/perl -w

use CGI::Carp qw(fatalsToBrowser);

print "Content-type: text/plain\n\n";
printf "Perl %vd", $^V;
