<?php
// this file creates a new event in a calendar

//TODO: this file should validate all the fields.

$calendar = $_POST['calendar'];
// date stuff
$hour = $_POST['hour'];
$month = $_POST['month'];
$day = $_POST['day'];
$year = $_POST['year'];
$minute_code = $_POST['minute'];
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
// attendee stuff
$name = $_POST['name'];
$email = $_POST['email'];
// summary stuff
$reason = $_POST['reason'];
$note = $_POST['note'];

$startTime = strtotime("$year-$month-$day $hour:$minute:00");

require_once('functions.inc.php');

$result = scheduleMeeting($calendar, $startTime, $reason, $note, $name, $email);

if ($result)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title>CS Office Hours - Scheduled</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
	<div class="event">
		<h2>Meeting successfully scheduled</h2>
		<pre><?php echo $result; ?></pre>
	</div>
	<p>an email has been sent to [insert professor name] and you as a reminder</p>
	<a href="./?cal=<?php echo $calendar; ?>">main pageish</a>
</body>
</html>

<?php
}
else
{
	echo "_alert: there has been a failure somewhere.";
}

?>
