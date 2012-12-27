<?php

// ******************************** New Relic ********************************
//
// PHP Agent Diagnostic Tool v 0.1
// Author: Tony Mayse 
//
// ***************************************************************************
//
//    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
//    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
//    MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
//    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
//    LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
//    OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
//    WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
//
// ***************************************************************************

// CONSTANTS

define("nrDebug",		TRUE);
define("nrPHPdiagVer",	"0.1");
define("nrLogFile",     "/tmp/nrPHPdiag.log");
define("nrDiagFile",     "/tmp/nrPHPdiagFiles.tar.gz");

// FUNCTIONS

if(nrDebug) echo "creating functions ";

if(nrDebug) echo "nrCSS";

// return inline CSS
function nrCSS(){
	$nrCSS = "";
	// add some CSS output here
	return($nrCSS);
}

if(nrDebug) echo "timestamp ";

// return constructed human-readable timestamp
function timestamp(){
	if(nrDebug) echo "getting date ";
	$theTime = getdate();

	$timestampNow .= $theTime[weekday]." ";
	$timestampNow .= $theTime[month]." ";
	$timestampNow .= $theTime[mday].", ";
	$timestampNow .= $theTime[year]." - ";
	$timestampNow .= $theTime[hours]." : ";
	$timestampNow .= $theTime[minutes]." : ";
	$timestampNow .= $theTime[seconds]." : ";
	if(nrDebug) echo "returning date ";

	return($timestampNow);
}

if(nrDebug) echo "nrInitLog ";
// zero log file and write header
// exit script if unable to write to logfile
function nrInitLog(){
	if(nrDebug) echo "initializing log ";
	if(is_writable(nrLogFile) && ! is_dir(nrLogFile)){
		$nrLogFileHandle = fopen(nrLogFile, "w");

		$initMessage = "***************************************************************************\n";
		$initMessage .= "Version: " . nrPHPdiagVer . "\n" ;
		$initMessage .= "Start Time:" . timestamp() . "\n\n";


		fwrite($nrLogFileHandle,$initMessage);
		if(nrDebug) printf("Log Init Message : " . $initMessage);

		fclose($nrLogFileHandle);
		if(nrDebug) echo "log initialized ";
	}
	else { exit("Unable to open log file at " . nrLogFile ."\n" ); }
}

if(nrDebug) echo "nrOut";
// output data both to webpage & logfile
// handle HTML tags
function nrOut($tag,$msg){
	if(nrDebug) echo "nrOut ";
	$nrLogFileHandle = fopen (nrLogFile, "a");
	fwrite(nrLogFileHandle, $msg . "\n");
	fclose($nrLogFileHandle);

	if(! empty($tag)){
		$openTag = "<" . $tag . ">";
		$closeTag = "</" . $tag . ">";
	}
	else{
		$openTag = "";
		$closeTag = "";
	}

	printf($openTag . $msg . $closeTag . "\n") ;
}

if(nrDebug) echo "getting to dispatcher ";

if(empty($_GET)){
	printf("<html>\n");
	printf(nrCSS());
	printf("<body>\n");

	printf("<h1>New Relic PHP Agent Diagnostic Tool</h1>");
	printf("<br />");
	nrOut("h2","Basic System Info");
	printf("<ul>");

		nrOut("li","System : " . `uname -a`);
		nrOut("li","Hostname : " . gethostname());
		nrOut("li","Self : " . $_SERVER[PHP_SELF]);
		nrOut("li","Address : " . $_SERVER[SERVER_ADDR]);
		nrOut("li","Name : " . $_SERVER[SERVER_NAME]);
		nrOut("li","Root : " . $_SERVER[DOCUMENT_ROOT]);

	printf("</ul>");

	if (extension_loaded('newrelic')) { 
		nrOut("p","Extension is Loaded");
		nrOut("p","New Relic App Name: ". ini_get('newrelic.appname'));
		nrOut("p","New Relic License Key: ". ini_get('newrelic.license'));
		$daemon_running = shell_exec("ps -ef | grep newrelic-daemon | grep -v grep ");
		if(empty($daemon_running)){
			nrOut("strong","Daemon IS NOT Running");
			// check for newrelic.cfg
			// check for /var/log/newrelic/newrelic-daemon.log
			// make a connection on 127.0.0.1 33142 & emit "version" which will cause the connection to close
		}
		else{
			nrOut("","Daemon is Running");
			nrOut("pre",$daemon_running);
		}
		// create a diag application and generate metrics
	}
	else {
		nrOut("strong","Extension NOT Loaded");
	}

	printf("</body></html>");
}
else{printf($_GET);}

?>
