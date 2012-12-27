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

define("nrPHPdiagVer",	"0.1")
define("nrLogFile",     "/tmp/nrPHPdiag.log");
define("nrDiagFile",     "/tmp/nrPHPdiagFiles.tar.gz");

// FUNCTIONS

// return inline CSS
function nrCSS(){
	// add some CSS output here
}

// return constructed human-readable timestamp
function timestamp(){
	$theTime = getdate();

	$timestampNow .= $theTime[weekday]." ";
	$timestampNow .= $theTime[month]." ";
	$timestampNow .= $theTime[mday].", ";
	$timestampNow .= $theTime[year]." - ";
	$timestampNow .= $theTime[hours]." : ";
	$timestampNow .= $theTime[minutes]." : ";
	$timestampNow .= $theTime[seconds]." : ";

	return($timestampNow);
}

// zero log file and write header
// exit script if unable to write to logfile
function nrInitLog()){
	if(not is_dir(nrLogFile)){
		$nrLogFileHandle = fopen (nrLogFile, "w");

		$initMessage = "***************************************************************************\"n";
		$initMessage .= "Version: " . nrPHPdiagVer . "\n" ;
		$initMessage .= "Start Time:" . timestamp() . "\n\n";

		fwrite($nrLogFileHandle,$initMessage);

		fclose($nrLogFileHandle);
	}
	else { exit("Unable to open log file at " . nrLogFile ."\n" ); }
}

// output data both to webpage & logfile
// handle HTML tags
function nrOut(tag,msg){
	$nrLogFileHandle = fopen (nrLogFile, "a");
	fwrite(nrLogFileHandle, msg . "\n");
	fclose($nrLogFileHandle);

	if(! empty(tag)){
		$openTag = "<" . tag . ">";
		$closeTag = "</" . tag . ">";
	}
	else{
		$openTag = "";
		$closeTag = "";
	}

	echo $openTag . msg . $closeTag . "\n" ;
}


if(empty($_GET)){
	echo("<html>\n");
	echo(nrCSS());
	echo("<body>\n");

	echo("<h1>New Relic PHP Agent Diagnostic Tool</h1>");
	echo("<br />");
	nrOut("h2","Basic System Info");
	echo("<ul>");

		nrOut("li","System : " . `uname -a`);
		nrOut("li","Hostname : " . gethostname());
		nrOut("li","Self : " . $_SERVER[PHP_SELF]);
		nrOut("li","Address : " . $_SERVER[SERVER_ADDR]);
		nrOut("li","Name : " . $_SERVER[SERVER_NAME]);
		nrOut("li","Root : " . $_SERVER[DOCUMENT_ROOT]);

	echo("</ul>");

	if (extension_loaded('newrelic')) { 
		nrOut("","Extension is Loaded");
		nrOut("","New Relic App Name: ".newrelic.appname);
		nrOut("","New Relic License Key: ".newrelic.license);
		$daemon_running = shell_exec("ps -ef | grep newrelic-daemon | grep -v grep ")
		if empty($daemon_running){
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

	echo("</body></html>");
}

?>
