<?php
/**
 * This file contains a class that provides factory methods for creating
 * table browsing objects using MDB2 instances.
 * 
 * Table browsing objects allow your code to handle any database table in an
 * abstract way. By freeing your code from the database details it is possible 
 * for you to build generic data reporting or manipulation functions.
 * 
 * Put another way, if you really hate using sql in your code, having to piece 
 * together bits of sql to make queries...this library gives you an alternative.
 * 
 * Currently only the single table browser is implemented. If you need to work 
 * with data that spans multiple tables, you can build a table view as this
 * library works with them as well.
 * 
 * PHP version 5
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   SVN:<svn_id>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser.php
 */
require_once 'PEAR/Exception.php';
require_once 'MDB2.php';

require_once 'MDB2/TableBrowser/Interfaces.php';
require_once 'MDB2/TableBrowser/ParameterException.php';
require_once 'MDB2/TableBrowser/DBException.php';
require_once 'MDB2/TableBrowser/TableValidator.php';
require_once 'MDB2/TableBrowser/FilterManager.php';
require_once 'MDB2/TableBrowser/ColumnManager.php';
require_once 'MDB2/TableBrowser/OrderByManager.php';
require_once 'MDB2/TableBrowser/GroupByManager.php';
require_once 'MDB2/TableBrowser/Single.php';


define('PACKAGE_NAME', 'MDB2_TableBrowser');

/**
 * Load MDB2 module class.
 * 
 * This class provides a factory method for creating single table browsers using
 * an existing mdb2 instance.
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser.php
 *
 */
class MDB2_TableBrowser extends MDB2_Module_Common
{
    /**
     * Factory method for creating a table browser
     *
     * @param string $tableName   The table name
     * @param string $primary_key Column that is the primary key
     * 
     * @return an MDB2_TableBrowser_Single object
     */
    function tableBrowserFactory($tableName, $primary_key=null, $caseSensitive = false)
    {
        $mdb2 =& $this->getDBInstance();
        return new MDB2_TableBrowser_Single($mdb2, $tableName, $primary_key, $caseSensitive);
    }
}

?>