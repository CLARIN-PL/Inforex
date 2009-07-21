function parseBlockUserCountsXml( xmldoc )
{
	var listElement = xmldoc.documentElement;
	if ( listElement.tagName != 'block-users' )
		return null;
	
	var counts = new Array();
	for ( var countElement = listElement.firstChild;  countElement;  countElement = countElement.nextSibling )
	{
		if ( ELEMENT_NODE == countElement.nodeType && 'element' == countElement.tagName )
		{
			var count = new UserCount( );
			count.fromXml( countElement );
			counts[ counts.length ] = count;
		}
	}
	return counts;
}

function UserCount( xpath, blockpath )
{
	this.users = new Array();
	this.xpath = xpath;
	this.blockpath = blockpath;
	this.url = null;
}

UserCount.prototype.fromXml = function( element )
{
	this.xpath = element.getAttribute( 'xpath' );
	this.blockpath = element.getAttribute( 'block' );
	this.url = element.getAttribute( 'url' );
	for ( var userElement = element.firstChild;  userElement;  userElement = userElement.nextSibling )
	{
		if ( ELEMENT_NODE == userElement.nodeType && 'user' == userElement.tagName )
			this.users[ this.users.length ] = getNodeText( userElement );
	}
}

UserCount.prototype.resolveBlock = function( root )
{
	if ( this.xpath )
	{
		var node = root.ownerDocument.evaluate( this.xpath, root, null, XPathResult.ANY_TYPE, null );
		return node.iterateNext();
	}
}
