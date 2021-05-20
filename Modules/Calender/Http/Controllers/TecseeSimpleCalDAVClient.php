<?php 

namespace Modules\Calender\Http\Controllers;
use it\thecsea\simple_caldav_client\SimpleCalDAVClient;
use it\thecsea\simple_caldav_client\CalDAVObject;
use Exception;
class TecseeSimpleCalDAVClient extends SimpleCalDAVClient  {
	
	function __construct() {
		$file = base_path() . DIRECTORY_SEPARATOR .'vendor'.DIRECTORY_SEPARATOR.'thecsea'.DIRECTORY_SEPARATOR.'simple-caldav-client'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'SimpleCalDAVClient.php';
		if(file_exists($file)){
			$filecontent = file_get_contents($file);
			$filecontent = str_replace("private","protected",$filecontent);
			file_put_contents($file, $filecontent);
		}else {
			die("DAMN ERROR");
		}
	}	
	
	function getEvents ( $start = null, $end = null )
	{
		// Connection and calendar set?
		if(!isset($this->client)) throw new Exception('No connection. Try connect().');
		if(!isset($this->client->calendar_url)) throw new Exception('No calendar selected. Try findCalendars() and setCalendar().');

		// Are $start and $end in the correct format?
		if ( ( isset($start) and ! preg_match( '#^\d\d\d\d\d\d\d\dT\d\d\d\d\d\dZ$#', $start, $matches ) )
		  or ( isset($end) and ! preg_match( '#^\d\d\d\d\d\d\d\dT\d\d\d\d\d\dZ$#', $end, $matches ) ) )
		{ trigger_error('$start or $end are in the wrong format. They must have the format yyyymmddThhmmssZ and should be in GMT', E_USER_ERROR); }

		// Get it!
		$results = $this->client->GetEvents( $start, $end );

		// GET-request successfull?
		if ( $this->client->GetHttpResultCode() != '207' )
		{
			throw new Exception('Recieved unknown HTTP status');
		}

		// Reformat
		$report = array();
		foreach($results as $event) $report[] = new CalDAVObject($this->url.$event['href'], $event['href'], $event['etag']);

		return $report;
	}
}