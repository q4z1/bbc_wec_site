/**
 * login
 *
 * Validierung des Formulars
 */
$(document).ready
(
	function()
	{
		if($('div.login').length > 0)
		{
			$('#signin').click
			(
				function(event)
				{
					if(typeof event !== "undefined" && event !== null)
					{
						if(event.preventDefault){ event.preventDefault(); }else{event.returnValue = false;}
						if(event.stopPropagation){event.stopPropagation();}else{event.cancelBubble = true;}
					}
					validate()
				}
			);
			$('#login_form').bind
			(
				'submit',
				function(event)
				{
					if(typeof event !== "undefined" && event !== null)
					{
						if(event.preventDefault){ event.preventDefault(); }else{event.returnValue = false;}
						if(event.stopPropagation){event.stopPropagation();}else{event.cancelBubble = true;}
					}
					validate();
				}
			);
		}
	}
);

/*
 * validiere Formular
 */
function validate()
{
	var state = true;
	var notice = "";
	/*
	 * Passwort lang genug?
	 */
	if($('#password').val().length < 4)
	{
		notice = 'Passwort muss mindestens 4 Zeichen lang sein!';
		state = false;
	}
	/*
	 * wenn ein Fehler aufgetreten ist, gib notice aus
	 */
	if(state === false)
	{
		$('#notice').html(notice);
		$('#notice').css('display', 'block');
		return;
	}
	else
	{
		$('#notice').html();
		$('#notice').css('display', 'none');
	}
	/*
	 * im Erfolgsfall Passworthash und submit
	 */
	$('#passhash').val($.md5($('#password').val()));
	$('#password').attr('disabled', 'disabled');
	$('#login_form').unbind('submit');
	$('#login_form').submit();
	return;
}

