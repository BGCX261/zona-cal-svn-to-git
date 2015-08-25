<?

define("BASE_PATH", "/Users/drake/Sites/zona-cal/");

function isAdmin() {
	return ($_GET['admin'] == 'weak');
}

function createCalendar($name, $email, $password) {
	// add entry to reference file
	$encryptedPassword = crypt($password, $email);
	$calendarFile = calendarFileName($email);

	$data = array(
		$email,
		$name,
		$encryptedPassword,
		$calendarFile
		);
		
	$handle = referenceFileOpen("a");
	$output = implode(',', $data) . "\n";
	fwrite($handle, $output);
	fclose($handle);
	
	// create the actual calendar
	$path = BASE_PATH . "calendars/" . $calendarFile . ".ics";
	fopen($path, "w") 
		or die("<br><br>_alert! this process did not complete all the way! the calendar file was not created! please COPY \"none.ics\" to be named \"".$calendarFile.".ics\" in the proper calendar directory (wherever that is). sorry for the trouble. this is a nasty but known feature. it is on the list of things to fix.");
	//TODO: fix this so it actually creates a file.
}

// this will check if you are a valid user and return the calendar filename
//   if you are a valid user and false if no user is found
function validateUser($email, $password) {
	$handle = referenceFileOpen("r");
	while ($data = fgetcsv($handle, 2000))
	{
		if ($data[0] == $email && $data[2] == crypt($password, $email))
			return $data[3];
	}
	fclose($handle);
	return false;
}

function referenceFileOpen($mode) {
	$path = BASE_PATH . 'admin/ref.txt';
	return fopen($path, $mode);
}

function calendarFileName($email) {
	$text = explode('@', $email);
	$filename = $text[0];
	// .ics extension will be added later for security reasons
	return $filename;
}

function scheduleMeeting($calendar, $startTime, $reason, $note, $name, $email) {
	$endTime = strtotime("+ 15 minutes", $startTime);
	$status = timeAvailable($calendar, $startTime, $endTime);
	if ($status == -1)
		return false;
	
	$attendee = $name . " - " . $email;
	$eventText = $reason . " - " . $note;
	// all meetings scheduled are private
	$class = 'PRIVATE';
	// all meetings are new events, so the uid is blank/new
	$uid = "";
	// the professor is now busy during this time, mark it as so.
	$categories = "UNAVAILABLE";
	
	$eventStart = date("Ymd", $startTime) . 'T' . date("His", $startTime);
	$eventEnd = date("Ymd", $endTime) . 'T' . date("His", $endTime);
	
	if ($status == 1)
	{
		return createEvent($uid, $calendar, $eventStart, $eventEnd, $eventText, $class, $attendee, $categories);
	}
	else if ($status == 0)
	{
		// on demand, you tricky case you.
		// give it a unique uid TODO: look this up to make sure i am doing it right.
		$uid = uniqid('-', true);
		// save the pending event to a file
		writePendingEvent($uid, $calendar, $eventStart, $eventEnd, $eventText, $class, $attendee, $categories);
		
		
		// send an email to a professor with a link to create the event or deny it
		// event is not identified by uid.
		$professor = infoForCalendar($calendar);
		$body = "Hello " . $professor['name'] . ",\n";
		$body .= $name . " would like to schedule a meeting with you\n";
		$body .= "on " . date("l", $startTime) . " the " . date("d", $startTime) . " of " . date("F", $startTime) . "\n";
		$body .= "at " . date("g:i a", $startTime) . "\n\n";
		//TODO: add correct links
		$body .= "accept - <link to event accept page>\n";
		$body .= "decline - <link to event decline page>\n";
		if (!sendEmail($professor['email'], "Meeting Requested", $body))
			return false;
		
		//TODO: fix!
		return "on_demand";
	}
}

function writePendingEvent($uid, $calendar, $eventStart, $eventEnd, $eventText, $class, $attendee, $categories) {
	$handle = openPendingFile("a");
	$out = "$uid,$calendar,$eventStart,$eventEnd,$eventText,$class,$attendee,$categories";
	return fwrite($handle, $out);
}
function openPendingFile($mode) {
	//TODO: get this filename from a config file
	$filename = BASE_PATH . "pending.txt";
	return fopen($filename, $mode);
}
function createEventFromPending($uid) {
	// load in the file
	$handle = openPendingFile("r");
	while ($line = fgets($handle))
	{
		if ($uid == $line.substr(0, $uid.length()))
		{
			$data = explode(',', $line);
			// if approved
			if ($decision = 'y')
			{
				createEvent("", $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7]);
				return true;
			}
			// if rejected. else should be okay because it was validated above
			else
			{
				// send email to student notifying them of the rejection. fix!
				$student = infoForAttendee($data[6]);
				$body = "Hello " . $student['name'] . ".\n";
				$body .= "Your meeting was declined.\n";
				$body .= "Better luck next time!\n";
				sendEmail($student['email'], "Meeting Declined", $body);
				return true;
			}
		}
	}
	return false;
}

function scheduleEvent($calendar, $startTime, $endTime, $class, $summary, $categories, $recur = null) {
	//creating implies new, so the uid is blank/new
	$uid = "";

	$eventStart = date("Ymd", $startTime) . 'T' . date("His", $startTime);
	$eventEnd = date("Ymd", $endTime) . 'T' . date("His", $endTime);
	
	// nobody should be attending the event.
	$attendee = "";

	require_once(BASE_PATH . 'monket-cal/monket-cal-update.php');
	$result = doUpdate($uid, $calendar, $eventStart, $eventEnd, $summary, $class, $attendee, $categories, $recur);

	if (strpos($result, "success") != 0)
		return false;
	
	return true;
}
	
function infoForCalendar($calendar) {
	$handle = referenceFileOpen("r");
	while ($line = fgets($handle))
	{
		$parts = explode(",", $line);
		if (trim($parts[3]) == $calendar)
			return array('name' => $parts[1], 'email' => $parts[0]);
	}
	return "unknown";
}
function infoForAttendee($attendee) {
	$parts = explode(" - ", $attendee);
	return array("name" => $parts[0], "email" => $parts[1]);
}

// returns -1 on unschedulable
//		0 on on_demand
//		1 on available
// more intuative name might be [prior] status of time or something?
function timeAvailable($calendar, $eventStart, $eventEnd) {
	if (timeIsDuringHours($start, $end))
		return -1;

	$status = 1;
	$events = eventsDuringTime($eventStart, $eventEnd);
	foreach ($events as $event)
	{
		if (eventIsUnavailable($event))
			return -1;
		if (eventIsOndemand($event))
			$status = 0;
	}
	return $status;
}
function timeIsDuringHours($start, $end) {
	// can be set by user to determine valid hours in the day.
	// TODO: should be set by a config file
	$validHourStart = "08";
	$validHourEnd = "17";
	
	
	$hour = strtotime("H", $start);
	if ($validHourStart > $hour || $hour >= $validHourEnd)
		return false;
	$hour = strtotime("H", $end);
	//subtle difference here. notice the = sign
	if ($validHourStart > $hour || $hour > $validHourEnd)
		return false;
		
	return true;
}
function eventsDuringTime($start, $end) {
	global $master_array;
	$co_events = Array();
	$key = date("Ymd", $start);

	// potentials are all events occuring that same day
	$potentials = $master_array[$key];
	
	$start = date("Hi", $start);
	$end = date("Hi", $end);
	if (isset($potentials))
	{
		foreach ($potentials as $potential)
		{
			foreach ($potential as $event)
			{
				$start_test = $event['event_end'] >= $start && $start >= $event['event_start'];
				$end_test = $event['event_end'] >= $end && $end >= $event['event_start'];
				$inside_test = $event['event_start'] > $start && $event['event_end'] < $end;
				if ($start_test || $end_test || $inside_test)
				{
					$co_events[] = $event;
				}
			}
		}
	}
	return $co_events;
}

function eventIsUnavailable($event){
	return $event['categories'] == 2;
}
function eventIsAvailable($event){
	return $event['categories'] == 1;
}
function eventIsOndemand($event){
	return $event['categories'] == 3;
}

function createEvent($uid, $calendar, $eventStart, $eventEnd, $eventText, $class, $attendee, $categories) {
	require_once(BASE_PATH . 'monket-cal/monket-cal-update.php');
	$result = doUpdate($uid, $calendar, $eventStart, $eventEnd, $eventText, $class, $attendee, $categories);

	if (strpos($result, "success") != 0)
		return false;
	
	$professor = infoForCalendar($calendar);
	
	// the writing of this email should probably be done in a function
	// send email to scheduler
	$body = "A meeting has been scheduled.\n";
	$body .= $professor['name'] . " will be meeting with " . $name . "\n";
	$body .= "on " . date("l", $startTime) . " the " . date("d", $startTime) . " of " . date("F", $startTime) . "\n";
	$body .= "at " . date("g:i a", $startTime) . "\n";
	$body .= "\nThis meeting was scheduled via zona-cal";
	if (!sendEmail($email, "Meeting Scheduled", $body))
		return false;
	// send email to professor
	if (!sendEmail($professor['email'], "Meeting Scheduled", $body))
		return false;

	return $body;
}

function sendEmail($to, $subject, $body) {
	// TODO: cut the message length down to 70 chars per line.
	return mail($to, $subject, $body);
}

function debug_dump($data) {
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}

function html_calendarSelect($name, $selectedVal) {
	$handle = referenceFileOpen("r");
	echo "<select name=\"$name\" onchange=\"window.location='?cal='+this.value\">\n";
	// put in the default case
	echo "<option value=\"none\">Select a professor</option>\n";
	// print all calendars names
	while ($line = fgets($handle))
	{
		// get out the name and calendar from the file
		$fields = explode(',', $line);
		$value =trim($fields[3]);
		$name = $fields[1];

		//deal with the selected value
		if ($selectedVal == $value)
			$selected = " selected ";
		else
			$selected = "";

		//print the option statement
		echo "<option value=\"$value\"$selected>$name</option>\n";
	}
	echo "</select>\n";
}

?>