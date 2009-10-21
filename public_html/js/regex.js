function isAlphanumeric (s)
{   
	var regex = /^[a-zA-Z0-9ążśźęćńółĄŻŚŹĘĆŃÓŁ]$/;
	return regex.test(s)
}