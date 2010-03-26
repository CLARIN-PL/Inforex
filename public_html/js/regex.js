function isAlphanumeric (s)
{   
	var regex = /^[a-zA-Z0-9ążśźęćńółĄŻŚŹĘĆŃÓŁ]$/;
	return regex.test(s)
}

function isWhite(s)
{
	return s==' ' || s=="\n" || s=="\r" || s=="\t";
}