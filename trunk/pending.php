<?php
// this file just takes requests from the links the system has emailed out as pending.
// they are in response to on_demand sheduling. 

// this needs to be super secure. fix!

require_once('functions.inc.php');

// validate!!! fix
$uid = $_GET['uid'];
$decision = $_GET['approved'];


$handle = openPendingFile("r");
while ($line = fgets($handle))
{
	if ($uid == $line.substr(0, $uid.length()))
	{
		$data = explode(',', $line);
		// if approved
		if ($decision = 'y')
		{
			require_once('monket-calendar/monket-cal-update.php');
			// fix?
			doUpdate("", $data[1], $data[2], $data[4], $data[5], $data[6]);
		}
		// if rejected. else should be okay because it was validated above
		else
		{
			// TODO:
			// send email to student notifying them of the rejection
		}
		// the event was either added or declined. return success.
		return true;
	}
}




?>