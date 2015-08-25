function populate(data)
{
	$('s_year').value = data.substr(0, 4);
	$('s_month').value = data.substr(4, 2);
	$('s_day').value = data.substr(6, 2);
	$('s_hour').value = data.substr(8, 2);
	minute = data.substr(10, 2);
	index = 0;
	if (minute >= 15)
		index = 1;
	if (minute >= 30)
		index = 2;
	if (minute >= 45)
		index = 3;
	$('s_minute').value = index;
	highlightCell(data);
	updateTimeDisplay(data);
	if ($('e_year'))
	{
		$('e_year').value = $F('s_year');
		$('e_month').value = $F('s_month');
		$('e_day').value = $F('s_day');
		$('e_hour').value = Number($F('s_hour')) + 1;
		$('e_minute').value = $F('s_minute');
	}
}

var previous = "";
function highlightCell(cell)
{
	if (previous != "")
		$(previous).style.backgroundColor = "";
	$(cell).style.backgroundColor = "#99CCFF";
	previous = cell;
}

function updateTimeDisplay(date)
{
	var year = date.substr(0,4);
	var month = date.substr(4,2);
	var day = date.substr(6,2);
	// month is - 1 for offset. gosh darn 0 months.
	var time_string = new Date(year, month - 1, day);
	switch (time_string.getDay())
	{
		case 0:
			day = "Sunday";
			break;
		case 1:
			day = "Monday";
			break;
		case 2:
			day = "Tuesday";
			break;
		case 3:
			day = "Wednesday";
			break;
		case 4:
			day = "Thursday";
			break;
		case 5:
			day = "Friday";
			break;
		case 6:
			day = "Saturday";
			break;
		default:
			day = "_error!";
			break;
	}
	
	// get the day of the month
	day_num = time_string.getDate();
	last = day_num.toString().substr(-1,1);
	if (last == '1' && day_num != '11')
		day_num += "st";
	else if (last == '2' && day_num != '12')
		day_num += "nd";
	else if (last == '3' && day_num != '13')
		day_num += "rd";
	else
		day_num += "th";
	
	// deal with the am pm part of the time
	var hour = date.substr(8, 2);
	ampm = "am";
	if (hour >= 12)
	{
		ampm = "pm";
		// hooray for 12 hour clocks!
		if (hour > 12)
			hour -= 12;
	}
	time = hour + ':' + date.substr(10, 2) + " " + ampm;
	
	// display it!
	$('time_display').innerHTML = day + " the " + day_num + " at " + time;
}

function replaceRecur()
{
	html = "<td><label for=\"recur\">Recurs:</label></td>";
	html += "<td>";
	html += "M<input type=\"checkbox\" name=\"recur[]\" value=\"MO\" /> ";
	html += "T<input type=\"checkbox\" name=\"recur[]\" value=\"TU\" /> ";
	html += "W<input type=\"checkbox\" name=\"recur[]\" value=\"WE\" /> ";
	html += "R<input type=\"checkbox\" name=\"recur[]\" value=\"TH\" /> ";
	html += "F<input type=\"checkbox\" name=\"recur[]\" value=\"FR\" /> ";
	html += "</td>";
	$('recur_row').innerHTML = html;
	
	until_html = "<td><label>Until:</label></td><td>";
	until_html += "<input id=\"u_year\" name=\"uyear\" type=\"text\" size=\"4\" />";
	until_html += "/<input id=\"u_month\" name=\"umonth\" type=\"text\" size=\"2\" />";
	until_html += "/<input id=\"u_day\" name=\"uday\" type=\"text\" size=\"2\" />";
	until_html += "</td>";
	var until_row = $('event_form').insertRow(6);
	until_row.innerHTML = until_html;
}
