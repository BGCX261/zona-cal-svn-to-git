<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>New Calendar</title>
<link href="style.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<?

require_once("../functions.inc.php");

if ($_POST['submit'])
{
	// verify that the passwords are the same
	if ($_POST['pass1'] == $_POST['pass2'])
	{
		createCalendar($_POST['name'], $_POST['email'], $_POST['pass1']);
		$message = "Created " . $_POST['name'] . " successfully.";
	}
	else
	{
		// passwords were not the same, set error message
		$error = "passwords did not match. try again.";
	}	
}
?>
<body>
<? 
// print status information
if ($error) echo "<p class='error'>" . $error . "</p>";
if ($message) echo "<p class='message'>" . $message . "</p>"
?>
<form id="new_calendar" name="new_calendar" method="post" action="">
  <p>Name
    <input name="name" type="text" id="name" />
</p>
  <p>Email Address
    <input name="email" type="text" id="email" />
</p>
  <p>Password
    <input name="pass1" type="password" id="pass1" />
  </p>
  <p>Password Verification
    <input name="pass2" type="password" id="pass2" />
  </p>
  <p>
    <input type="submit" name="submit" value="Create" />
  </p>
</form>
</body>
</html>