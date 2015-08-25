<?php

        /*
         * Monket Calendar 0.9
         *  by Karl O'Keeffe
         *  24 June 2005
         *
         * Homepage: http://www.monket.net/wiki/monket-calendar/
         * Released under the GPL (all code)
         * Released under the Creative Commons License 2.5 (without phpicalendar)
         */

define('CALENDAR_DIR', '/Users/drake/Sites/zona-cal/calendars/');

function doUpdate($uid, $calName, $eventStart, $eventEnd, $eventText, $class, $attendee, $categories, $recur) {

	if ($calName == null)
		return "failed\nno calendar name";

	if ($uid == '' && ($eventStart == null || $eventEnd == null))
		return "failed\nno start/end date";

	if ($uid == '' && $eventText == '')
		return "success\nhaven't created event because text is empty";

	$filename = CALENDAR_DIR . $calName . '.ics';

	// backup calendar
	//if (!copy($filename, $filename . '.bak'))
	//	return "failed\nunable to backup calendar: $filename";

	// get calendar file specified
	if (!is_writable($filename)) 
		return "failed\ncalendar is not writeable: $filename";

	$lines = file($filename);
	if ($lines === FALSE)
		return "failed\nunable to read in calendar: $filename";

	$handle = fopen($filename, 'w');
	if ($handle == null)
		return "failed\nunable to open calendar file for writing: $filename";

	$result = "failed:\nunknown reason";
	// if there is no uid, create a new event
	if ($uid == '')
	{
		$uid = uniqid('ZONACAL-', true);

		//   create ical record
		unset($record);
		$record .= "BEGIN:VEVENT\n";
		$record .= "DTSTART;VALUE=DATE-TIME:" . $eventStart .  "\n";
		$record .= "DTEND;VALUE=DATE-TIME:" . $eventEnd . "\n";
		$record .= "SUMMARY:" . $eventText . "\n";
		$record .= "UID:" . $uid . "\n";
		$record .= "DTSTAMP:" . date('Ymd\THis') . "\n";
		$record .= "CLASS:" . $class . "\n";
		$record .= "ATTENDEE:" . $attendee . "\n";
		$record .= "CATEGORIES:" . $categories . "\n";
		
		// add in recur functionality!i!i!i!
		if ($recur)
		{
			$record .= "RRULE:FREQ=WEEKLY;INTERVAL=1;UNTIL=".$recur['until'].";BYDAY=".$recur['days'].";WKST=MO\n";
			//MO,TU,WE,TH,FR
			//20071031T055959Z
			//DURATION:PT1H
		}
		
		$record .= "END:VEVENT\n";
		
		// hit the end of the calendar. when that happens insert our new event.
		// this should now be the last event in the file. then rewrite the end
		// of the calendar. close the file
		$result = "failed\ndid not write record";
		foreach ($lines as $line)
		{
			if (trim($line) == 'END:VCALENDAR')
			{
				$result = 'success' . "\n" . $uid;
				fputs($handle, $record);
			}
			fputs($handle, $line);
		}

	}
	// otherwise edit the event 
	else 
	{
		$result = "failed\nunable to edit event";
		$record = null;
		foreach ($lines as $line) 
		{
			$value = trim($line);
			
			if ($value == 'BEGIN:VEVENT') 
			{
				$record = $line;
			} 
			else if (startsWith($value, 'UID:')) 
			{
				$record .= $line;
				$recordUid = trim(substr($value, strlen('UID:')));
			} 
			else if ($value == 'END:VEVENT') 
			{
				$record .= $line;
				if ($uid == $recordUid) 
				{
					$record = updateRecord($record, $eventText, $eventStart, $eventEnd);
				}
				fputs($handle, $record);
				$record = null;
				$recordUid = null;
				$result = "success";
			} 
			else if ($record !== null) 
			{
				$record .= $line;
			} 
			else 
			{
				fputs($handle, $line);
			}
		}
	}

	fclose($handle);
	return $result;
}

function startsWith($string, $substring) {
	return (substr($string, 0, strlen($substring)) == $substring);
}

function updateRecord($record, $eventText, $eventStart, $eventEnd) {
	if ($eventText !== null && trim($eventText) == '')
		return null;

// TODO: fix this so it updates the proper fields. though this may not be necessary as we should never have to update a meeting
	$newRecord = '';
	$recordArray = split("\n", $record);
	foreach ($recordArray as $line)
	{
		if ($eventText !== null && startsWith($line, 'SUMMARY:'))
			$line = 'SUMMARY:' . $eventText;
		if ($eventStart !== null && startsWith($line, 'DTSTART'))
			$line = 'DTSTART;VALUE=DATE:' . $eventStart;
		if ($eventEnd !== null && startsWith($line, 'DTEND'))
			$line = 'DTEND;VALUE=DATE:' . $eventEnd;
		$newRecord .= $line . "\n";
	}		
	return trim($newRecord) . "\n";
}

?>
