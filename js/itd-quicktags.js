if ( typeof itd_get_attributes != 'undefined' )
{
	var attributes = itd_get_attributes();
	edButtons[edButtons.length] =
	new edButton('ed_direction'
		,'><'
		,'<span ' + attributes + '>'
		,'</span>'
		,'e'
	);
}