/*
 * prefs.js
 *
 * Marginalia has been developed with funding and support from
 * BC Campus, Simon Fraser University, and the Government of
 * Canada, and units and individuals within those organizations.
 * Many thanks to all of them.  See CREDITS.html for details.
 * Copyright (C) 2005-2007 Geoffrey Glass www.geof.net
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
 *
 * $Id: prefs.js 272 2007-11-26 04:14:16Z geof.glass $
 */

AN_USER_PREF = 'annotations.user';
AN_SHOWANNOTATIONS_PREF = 'annotations.show';
AN_NOTEEDITMODE_PREF = 'annotations.note-edit-mode';
AN_SPLASH_PREF = 'annotations.splash';
SMARTCOPY_PREF = 'smartcopy';


/**
 * Preferences creation
 */
function Preferences( service, prefs )
{
	this.preferences = new Object( );
	this.service = service;
	
	if ( prefs )
	{
		for ( name in prefs )
			this.preferences[ name ] = prefs[ name ];
	}
}

/**
 * Fetch preferences from the service.  (though it's preferable to simply pass prefs in to the constructor)
 * Call this as soon as possible to prevent blocking in getPreference
 * continueFunction - pass this in order to execute that function once all preferences
 *  are fetched.  This is the best way to ensure you don't try to get a preference before
 *  it has been retrieved from the server.
 */
Preferences.prototype.fetch = function( continueFunction )
{
	this.continueFunction = continueFunction;
	var prefs = this;
	_cachePrefs = function( text )
	{
		prefs.cachePreferences( text );
	}
	this.service.listPreferences( _cachePrefs );
}

Preferences.prototype.cachePreferences = function( text )
{
	var lines = text.split( "\n" );
	for ( var i = 0;  i < lines.length;  ++i )
	{
		var x = lines[ i ].indexOf( ':' );
		if ( -1 != x )
		{
			var name = lines[ i ].substr( 0, x );
			var value = lines[ i ].substr( x + 1 );
			this.preferences[ name ] = value;
			trace( 'prefs', 'Preference:  ' + name + ' = "' + value + '"' );
		}
	}
	if ( this.continueFunction )
	{
		var continueFunction = this.continueFunction;
		delete this.continueFunction;
		continueFunction( );
	}
}

/**
 * Fetch the value of an individual preference
 * Make sure the preferences have been fetched by the init routine before calling this
 */
Preferences.prototype.getPreference = function( name, defaultValue )
{
	return this.preferences[ name ] ? this.preferences[ name ] : defaultValue;
}


Preferences.prototype.setPreference = function( name, value )
{
	// Only set the preference if it has changed (saves HTTP requests)
	if ( ! this.preferences[ name ] || this.preferences[ name ] != value )
		this.service.setPreference( name, value );
	this.preferences[ name ] = value;
}

