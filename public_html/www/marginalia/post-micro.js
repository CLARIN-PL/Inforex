/*
 * post-micro.js
 *
 * Support for message / blog post micro-format.  This is based on the
 * hAtom microformat.
 *
 * Marginalia has been developed with funding and support from
 * BC Campus, Simon Fraser University, and the Government of
 * Canada, the UNDESA Africa i-Parliaments Action Plan, and  
 * units and individuals within those organizations.  Many 
 * thanks to all of them.  See CREDITS.html for details.
 * Copyright (C) 2005-2007 Geoffrey Glass; the United Nations
 * http://www.geof.net/code/annotation
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
 * $Id: post-micro.js 327 2008-11-26 23:31:50Z geof.glass $
 */

// These class names will change once there's a microformat standard.
PM_POST_CLASS = 'hentry';				// this is an addressable fragment for annotation
PM_CONTENT_CLASS = 'entry-content';	// the content portion of a fragment
PM_TITLE_CLASS = 'entry-title';		// the title of an annotated fragment
PM_AUTHOR_CLASS = 'author';			// the author of the fragment
PM_DATE_CLASS = 'published';			// the creation/modification date of the fragment
PM_URL_REL = 'bookmark';				// the url of this fragment (uses rel rather than class attribute)

/*
 * This class keeps track of PostMicro stuff on a page
 * Initially that information was integrated into individual DOM nodes (especially
 * as PostMicro objects), but because of memory leak problems I'm moving it here.
 */
function PostPageInfo( doc )
{
	this.doc = doc;
	this.posts = new Array( );
	this.postsById = new Object( );
	this.postsByUrl = new Object( );
	this.IndexPosts( doc.documentElement );
}

/**
 * In order to avoid creating multiple instances for a given document,
 * keep a cache in the window.  The linear search here shouldn't be a problem
 * as I don't expect more than one to exist - but just in case, it's there.
 */
PostPageInfo.getPostPageInfo = function( doc )
{
	var info;
	if ( window.PostPageInfos )
	{
		for ( var i = 0;  i < window.PostPageInfos.length; ++i )
		{
			info = PostPageInfos[ i ];
			if ( info.doc == doc )
				return info;
		}
	}
	else
		window.PostPageInfos = [ ];
	info = new PostPageInfo( doc );
	window.PostPageInfos[ window.PostPageInfos.length ] = info;
	return info;
}

PostPageInfo.prototype.IndexPosts = function( root )
{
	var posts = domutil.childrenByTagClass( root, null, PM_POST_CLASS, null, PostMicro.skipPostContent );
	for ( var i = 0;  i < posts.length;  ++i )
	{
		var postElement = posts[ i ];
		var post = new PostMicro( this, postElement );
		this.posts[ this.posts.length ] = post;
		if ( null != posts[ i ].id && '' != posts[ i ].id )
			this.postsById[ posts[ i ].id ] = post;
		if ( null != post.getUrl( ) && '' != post.getUrl( ) )
			this.postsByUrl[ post.getUrl( ) ] = post;
		postElement.post = post;
	}
}

PostPageInfo.prototype.getPostById = function( id )
{
	return this.postsById[ id ];
}

/*
 * Return a post with a matching URL or, if that does not exist, try stripping baseUrl off the passed Url
 */
PostPageInfo.prototype.getPostByUrl = function( url, baseUrl )
{
	if ( this.postsByUrl[ url ] )
		return this.postsByUrl[ url ];
	else if ( baseUrl && url.substring( 0, baseUrl.length ) == baseUrl )
		return this.postsByUrl[ url.substring( baseUrl.length ) ];
	// Only try prepending base url if there's no scheme on the URL
	else if ( ! url.match( /^[a-z]+:\/\// ) )
		return this.postsByUrl[ baseUrl + url ];
}

PostPageInfo.prototype.getAllPosts = function( )
{
	return this.posts;
}

PostPageInfo.prototype.getPostMicro = function( element )
{
	var post = null;
	
	if ( element.post )
		post = element.post;
	else
	{
		var postElement = null;
		for ( var node = element;  node;  node = node.parentNode )
		{
			if ( ! postElement && ELEMENT_NODE == node.nodeType && domutil.hasClass( node, PM_POST_CLASS ) )
				postElement = node;
			else if ( PostMicro.skipPostContent( node ) )
				postElement = null;
		}
		if ( postElement )
		{
			if ( ! postElement.post )
				postElement.post = new PostMicro( this, postElement );
			post = postElement.post;
		}
	}
	return post;
}


/*
 * Post Class
 * This is attached to the root DOM node of a post (not the document node, rather the node
 * with the appropriate class and ID for a post).  It stores references to child nodes
 * containing relevant metadata.  The class also provides a place to hang useful functions,
 * e.g. for annotation or smart copy support.
 */
function PostMicro( postInfo, element )
{
	// Point the post and DOM node to each other
	this._element = element;
}

/*
 * For ignoring post content when looking for specially tagged nodes, so that authors
 * of that content (i.e. users) can't mess things up.
 */
PostMicro.skipPostContent = function( node )
{
	return ( ELEMENT_NODE == node.nodeType && domutil.hasClass( node, PM_CONTENT_CLASS ) );
}


/*
 * Accessor for related element
 * Used so that we can avoid storing a pointer to a DOM node,
 * which tends to cause memory leaks on IE.
 */
PostMicro.prototype.getElement = function( )
{
	return this._element;
}


PostMicro.prototype.getTitle = function( )
{
	if ( ! this._fetchedTitle )
	{
		// The title
		var metadata = domutil.childByTagClass( this._element, null, PM_TITLE_CLASS, PostMicro.skipPostContent );
		this._title = metadata == null ? '' : domutil.getNodeText( metadata );
		this._fetchedTitle = true;
	}
	return this._title;
}

PostMicro.prototype.getAuthorId = function( )
{
	if ( ! this._fetchedAuthorId )
	{
		// The author
		metadata = domutil.childByTagClass( this._element, null, PM_AUTHOR_CLASS, PostMicro.skipPostContent );
		this._authorId = metadata == null ? '' : metadata.getAttribute( 'title' );
		this._fetchedAuthorId = true;
	}
	return this._authorId;
}

PostMicro.prototype.getAuthorName = function( )
{
	if ( ! this._fetchedAuthorName )
	{
		// The author
		metadata = domutil.childByTagClass( this._element, null, PM_AUTHOR_CLASS, PostMicro.skipPostContent );
		this._authorName = metadata == null ? '' : domutil.getNodeText( metadata );
		this._fetchedAuthorName = true;
	}
	return this._authorName;
}

PostMicro.prototype.getDate = function( )
{
	if ( ! this._fetchedDate )
	{
		metadata = domutil.childByTagClass( this._element, 'abbr', PM_DATE_CLASS, PostMicro.skipPostContent );
		if ( null == metadata )
			this._date = null;
		else
		{
			var s = metadata.getAttribute( 'title' );
			if ( null == s )
				this._date = null;
			else
			{
				var matches = s.match( /(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})([+-]\d{4})/ );
				if ( null == matches )
					this._date = null;
				else
					// I haven't figured out how to deal with the time zone, so it assumes that server
					// time and local time are the same - which is rather bad.
					this._date = new Date( matches[1], matches[2]-1, matches[3], matches[4], matches[5] );
			}
		}
		this._fetchedDate = true;
	}

	return this._date;
}

PostMicro.prototype.getUrl = function( baseUrl )
{
	if ( ! this._fetchedUrl )
	{
		// The node containing the url
		metadata = domutil.childAnchor( this._element, PM_URL_REL, PostMicro.skipPostContent );
		this._url = metadata.getAttribute( 'href' );
		this._fetchedUrl = true;
	}
	return ( baseUrl && this._url.substring( 0, baseUrl.length ) == baseUrl )
		? this._url.substring( baseUrl.length )
		: this._url;
}

/*
 * Accessor for content element
 * Used so that we can avoid storing a pointer to a DOM node,
 * which tends to cause memory leaks on IE.
 */
PostMicro.prototype.getContentElement = function( )
{
	if ( ! this._contentElement )
	{
		// The node containing the content
		// Any offsets (e.g. as used by annotations) are from the start of this node's children
		this._contentElement = domutil.childByTagClass( this._element, null, PM_CONTENT_CLASS );
	}
	return this._contentElement;
}


