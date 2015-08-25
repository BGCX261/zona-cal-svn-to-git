<?php
// this file creates a new event in a calendar

require_once("../functions.inc.php");

//TODO: this file should validate all the fields.

$calendar = $_POST['cal'];
// date stuff - this could probably be abstracted better. two are so similar.
$hour = $_POST['shour'];
$month = $_POST['smonth'];
$day = $_POST['sday'];
$year = $_POST['syear'];
$minute_code = $_POST['sminute'];
switch ($minute_code)
{
	case '0':
		$minute = '00';
		break;
	case '1':
		$minute = '15';
		break;
	case '2':
		$minute = '30';
		break;
	case '3':
		$minute = '45';
		break;
	default:
		$minute = '00';
		break;
}
$startTime = strtotime("$year-$month-$day $hour:$minute:00");

$hour = $_POST['ehour'];
$month = $_POST['emonth'];
$day = $_POST['eday'];
$year = $_POST['eyear'];
$minute_code = $_POST['eminute'];
switch ($minute_code)
{
	case '0':
		$minute = '00';
		break;
	case '1':
		$minute = '15';
		break;
	case '2':
		$minute = '30';
		break;
	case '3':
		$minute = '45';
		break;
	default:
		$minute = '00';
		break;
}
$endTime = strtotime("$year-$month-$day $hour:$minute:00");

// attendee stuff
$class = $_POST['class'];
$summary = $_POST['summary'];
// summary stuff
$categories = $_POST['categories'];

//TODO: implement the parsing of the recur data!i!i!
$recur['days'] = implode(",", $_POST['recur']);
$recur['until'] = $_POST['uyear'] . $_POST['umonth'] . $_POST['uday'];

if (scheduleEvent($calendar, $startTime, $endTime, $class, $summary, $categories, $recur))
{
	echo "eventually there will be a redirect here. success!";
	//header("Location: ");
	echo "<a href=\"../?cal=" . $calendar . "&admin=" . $_POST['admin'] . "\">cool</a>";
}
else
{
	echo "_alert: there has been a failure somewhere.";
}

?>
