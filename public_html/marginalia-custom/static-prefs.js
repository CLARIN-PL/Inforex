/*
 * static-prefs.js
 * Static implementation of preference service for Marginalia
 *
 * Marginalia has been developed with funding and support from
 * BC Campus, Simon Fraser University, and the Government of
 * Canada, and units and individuals within those organizations.
 * Many thanks to all of them.  See CREDITS.html for details.
 * Copyright (C) 2005 Geoffrey Glass www.geof.net
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

function StaticPreferenceService( )
{
	return this;
}

/**
 * Fetch all preferences for the current user
 * There is little point in implementing methods to fetch individual preferences,
 * because the call is asynchronous.  Instead, the preference service should
 * cache the results.
 */
StaticPreferenceService.prototype.listPreferences = function( f )
{
	// Not all of these are used by the stand-alone version, but might as well
	// define them anyway.
	dummyResponseText =
		PREF_SHOWANNOTATIONS + ":true\n"
		+ PREF_SHOWUSER + ":anonymous\n"
		+ PREF_NOTEEDIT_MODE + ':' + AN_EDIT_NOTE_KEYWORDS + "\n";
	f ( dummyResponseText );
}

StaticPreferenceService.prototype.setPreference = function( setting, value, f )
{
	// This is a dummy implementation, so do nothing.
	return;
}
