<?php
require('../functions.inc.php');


if ($_POST['submit'])
{
	// if they are the master, redirect them to the create new calendar page
	if ($_POST['login'] == 'master')
		header('Location: ./new_calendar.php');
	// otherwise try to log them in as an already existing person
	else
	{
		if ($calendar = validateUser($_POST['login'], $_POST['password']))
		{
			echo "login success";
			echo '<p><a href="../?cal='.$calendar.'&admin=weak">cool</a></p>';
			// this is a lazy hack here.
			exit();
			//header('Location: ./?cal=$calendar&admin=weak');
		}
	}
	// if they have hit here something must have gone wrong. tell them that.
	$message = "something went wrong. try again.";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>CS Office Hours - Admin</title>
	<link rel="stylesheet" type="text/css" href="../style.css" />
</head>

<body>
<?php
echo "<h2>May the 4th be with you</h2>";
if ($message)
	echo "<p>" . $message . "</p>";
?>
<div id="leftCol">
	
<form action="" method="post">
	<table><tr>
	<td><label for="login">Email:</label></td>
	<td><input type="text" name="login" value="" /></td>
		</tr><tr>
	<td><label for="password">Password:</label></td>
	<td><input type="password" name="password" value="" /></td>
</tr><tr>
	<td>&nbsp;</td><td><input type="submit" value="Continue &rarr;" name="submit" /></td>
	</tr></table>
</form>

</div>
</body>
</html>