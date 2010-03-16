<?php
/**
 * This file contains a class used to catch db generated errors and throw exceptions
 * 
 * PHP version 5
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   SVN:<svn_id>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/D.php
 */

/**
 * MDB2_TableBrowser_DBException is a simple extention of PEAR_Exception
 * 
 * It is used by other classes generate exceptions when encountering an
 * mdb2 generated error. It allows for the actual MDB2_Error object to be 
 * returned as well
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/ColumnManager.php
 */
class MDB2_TableBrowser_DBException extends PEAR_Exception
{
    //Stored an MDB2_Error object
    protected $errorObj;
    
    /**
     * Constructor
     * 
     * @param MDB2_Error $mdb2error The error object
     */
    public function __construct($mdb2error=null)
    {
        $this->errorObj = $mdb2error;
        $this->message  = $mdb2error->getMessage();
        // make sure everything is assigned properly
        parent::__construct($this->message, 0);
    }
    /**
     * Returns the MDB2_Error object
     *
     * @return instace of MDB2_Error
     */
    public function getError()
    {
        return $this->errorObj;
    }
    
    /**
     * Produces a stringified error message
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>