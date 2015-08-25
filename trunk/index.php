<?php require_once('functions.inc.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>CS Office Hours</title>
  <link rel="stylesheet" type="text/css" href="./phpicalendar/templates/drake/default.css" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script language="javascript" src="./prototype.js"></script>
	<script language="javascript" src="./scripts.js"></script>
</head>

<body>
<div id="leftCol">
<?php
if (isAdmin())
{
?>
	<h2>Create an Event</h2>
	<form action="admin/create.php", method="post">
		<input type="hidden" id="s_cal" name="cal" value="<?php print(trim($_GET['cal'])); ?>" />
	<table id="event_form">
		<td><label>Date:</label></td>
	<td>
	<input id="s_year" name="syear" type="text" size="4" />/<input id="s_month" name="smonth" type="text" size="2" />/<input id="s_day" name="sday" type="text" size="2" /></td>
	</tr><tr>
		<td><label>Time:</label></td>
		<td>
	<input id="s_hour" name="shour" type="text" size="2" />:<select name="sminute" id="s_minute">
		<option value="0">00</option>
		<option value="1">15</option>
		<option value="2">30</option>
		<option value="3">45</option>
	</select> to <input id="e_hour" name="ehour" type="text" size="2" />:<select name="eminute" id="e_minute">
			<option value="0">00</option>
			<option value="1">15</option>
			<option value="2">30</option>
			<option value="3">45</option>
		</select>
		<input id="e_year" name="eyear" type="hidden" size="4" readonly />
		<input id="e_month" name="emonth" type="hidden" size="2" readonly />
		<input id="e_day" name="eday" type="hidden" size="2" readonly />
		
		<input id="time_display" type="hidden" /><!-- this is here because of crappy javascript programming. fix it sometime? -->
		</td>
		</tr><tr>
			<td><label for="categories">Availability:</label></td>
		  <td><select id="s_categories" name="categories">
		    <option value="AVAILABLE">Available</option>
		    <option value="ON_DEMAND">On Demand</option>
				<option value="UNAVAILABLE">Busy</option>
		  </select></td>
	</tr><tr>

			<td><label for="summary" style="valign:top;">Summary:</label></td>
		  <td><textarea id="s_summary" name="summary"></textarea></td>
		
				</tr><tr>
	<td><label for="class">Visibility:</label></td>
  <td><select id="s_class" name="class">
    <option value="PUBLIC">Public</option>
    <option value="PRIVATE">Private</option>
  </select></td>
		</tr><tr id="recur_row">
			<td>&nbsp;</td>
			<td><a href="javascript::void();" onclick="replaceRecur();">repeats weekly</a>
			</td>
		</tr><tr>
  <td>&nbsp;</td><td><input type="submit" name="Submit" value="Create Event" /></td>
</tr>
</table>
</form>
<?php
}
else
{
?>
	<h2>Schedule an Appointment</h2>
	<form action="schedule.php", method="post">
		<table>
			<tr>
  <td><label for="calendar">With:</label></td>
    <td><?php html_calendarSelect("calendar", $_GET['cal']); ?></td>
	</tr><tr>
		<td><label>At:</label></td>
	<td id="time_display">Select a time to the right --></td>
	</tr><tr>
	<td><label for="name">Name:</label></td>
	<td><input id="s_name" name="name" type="text" /></td>
	</tr><tr>
	<td><label for="email">Email:</label></td>
  <td><input id="s_email" name="email" type="text" /></td>
</tr><tr>
	<input id="s_year" name="year" type="hidden" />
	<input type="hidden" name="month" value="" id="s_month" />
	<input id="s_day" name="day" type="hidden" />
	<input id="s_hour" name="hour" type="hidden" />
	<input id="s_minute" name="minute" type="hidden" />

	<td><label for="reason">Reason:</label></td>
  <td><select id="s_reason" name="reason">
    <option>Class Help</option>
    <option>Advising</option>
		<option>Research</option>
		<option>CS Information</option>
		<option>Tea or MTdew</option>
    <option>Other</option>
  </select></td>
</tr><tr>
	<td><label for="note" style="valign:top;">Note:</label></td>
  <td><textarea id="s_note" name="note"></textarea></td>
</tr><tr>
  <td>&nbsp;</td><td><input type="submit" name="Submit" value="Schedule" /></td>
</tr>
</table>
</form>

<?php
//debug_dump(eventsDuringTime("1178636300","1178636400"));
}
?>

</div>

<div id="calendar">
  <?php 
	if (isset($_GET['cal']) && ($_GET['cal'] != "none" && $_GET['cal'] != ''))
	{
		include('./phpicalendar/week.php');
		//debug_dump(eventsDuringTime(strtotime("9:15"),strtotime("9:30")));
	}
	else
		include('default.htm');
	?>
</div>
<small><a href="admin" style="text-decoration:none;color:black;">admin</a></small>
<!-->
<div id="notes">
  <p>time will be divided into 15 minute sections</p>
  <p>it will be javascript enabled so if you click on the section on the calendar, the fields are automatically populated</p>
  <p>if an on demand section is selected, an email is sent to you with a link to confirm or deny the appointment. the student is then notified via email of the result and the calendar is updated accordingly</p>
  <p>all data will be stored in the icalandar ietf rfc format and should be exportable into outlook or ical. import and syncing? </p>
  <p>each professor will have a seperate calendar  </p>
  <p>the calendar will have 4 basic states. available, busy, ondemand and unscheduled. reasons can be made public or private for each of these states.</p>
  <p>scheduling the calendar for the professor backend will be much the same, except for that they will be able to have reoccuring events where an end date can be specified.</p>
  <p>does there have to be a way to verify the students?<br />
    this has potential to be a general scheduling device.
</p>
</div>
<-->
</body>
</html>
