<?php
/**
 * This file contains a class used to catch invalid parameter errors and throw exceptions
 * 
 * PHP version 5
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   SVN:<svn_id>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/ParameterException.php
 */

/**
 * MDB2_TableBrowser_ParameterException is an extention of PEAR_Exception
 * 
 * It is used by other classes generate exceptions when encountering an
 * a method receives invalid parameters. It adds a method that returns all the 
 * parameters passed to the method that called the exeption
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/ParameterException.php
 */
class MDB2_TableBrowser_ParameterException extends PEAR_Exception
{
    //Stores the paramaters passed to the calling method
    protected $parameters = null;
    //Stores the error message
    protected $errorMsg = null;
    
    // Redefine the exception so message is pulled from the DB
    /**
     * Constructor
     *
     * @param string $errorMsg   The error message
     * @param array  $parameters The paramaters passed to the calling method
     */
    public function __construct($errorMsg, $parameters)
    {
        $this->parameters = $parameters;
        $this->errorMsg   = $errorMsg;

        // make sure everything is assigned properly
        parent::__construct($this->errorMsg, 0);
    }
    /**
     * Returns the parameter passed to the method that called the exception
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    /**
     * Gets a stringified form of the error message
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": {$this->errorMsg}\n";
    }
}
?>