<?php
/**
 * This file contains a class that implements the MDB2_TableBrowser_InterfaceMultipleWhereClauseGenerator interface
 * 
 * PHP version 5
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   SVN:<svn_id>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/FilterManager.php
 */

require_once 'MDB2/TableBrowser/Interfaces.php';
require_once 'MDB2.php';
require_once 'MDB2/TableBrowser/DBException.php';
require_once 'MDB2/TableBrowser/FilterManager.php';

/**
 * This class implements the MDB2_TableBrowser_InterfaceMultipleWhereClauseGenerator interface
 * 
 * It is used by other classes to create and manage the where clause of an
 * SQL statement. As such, it's primary purpose is to add/remove filters which
 * are expressions that evaluate to true or false.
 * 
 * This class builds on top of MDB2_TableBrowser_FilterManager class. It adds
 * the ability to build multiple filter chains so that one can test for parralel
 * sets of conditions
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/ColumnManager.php
 */
class MDB2_TableBrowser_MultipleFilterManager implements MDB2_TableBrowser_InterfaceMultipleWhereClauseGenerator
{
    protected $validator          = null;
    protected $currentFilterChain = null;
    protected $filterChains       = array();
    
    //Array containing refrences to all external methods that will be dispatched using __call
    private $_externalMethods;
    /**
     * Constructor
     *
     * @param tableValidator &$validator A refrence to an object that implements the tableValidator interface
     */
    public function __construct(MDB2_TableBrowser_InterfaceTableValidator &$validator)
    {
        $this->validator = $validator;
        
        $this->filterChains[self::DEFAULT_CHAIN_NAME] = new MDB2_TableBrowser_FilterManager($validator);
        
        $this->currentFilterChain = self::DEFAULT_CHAIN_NAME;
        
        $this->_registerMethods();
    }
    /**
     * This method registers all the valid methods that will be called via the
     * __call() method. Each method is associated with the object it belongs to.
     * 
     * @return void
     *
     */
    private function _registerMethods()
    {
        $this->_externalMethods = array(
                                    'addFilter'         => $this->filterChains,
                                    'removeFilter'      => $this->filterChains,
                                    'resetFilters'      => $this->filterChains,
                                    'addCustomFilter'   => $this->filterChains,
                                    'createFilterChain' => $this->filterChains,
                                    'deleteFilterChain' => $this->filterChains,
                                    'selectFilterChain' => $this->filterChains,
                                    'resetAllFilters'   => $this->filterChains);
    }
    /**
     * Calls the method of the current selected filterChain object
     *
     * @param string $method The method name
     * @param array  $args   The method parameters 
     * 
     * @return mixed Depends on the method being called
     */
    public function __call($method, $args)
    {
        $currChain = $this->currentFilterChain;
        if (isset($this->_externalMethods[$method])) {
            $obj = $this->filterChains[$currChain];
            return call_user_func_array(array($obj, $method), array_merge(array($obj), $args));
        }
        //Unknown methods throw exception
        throw new MDB2_TableBrowser_ParameterException("Unknown Method $method", $args);
    }
    /**
     * Creates a new filter chain
     *
     * @param string $filterChainName The name of the filterChain
     * 
     * @throws MDB2_TableBrowser_ParameterException
     * 
     * @return void
     */
    public function createFilterChain($filterChainName)
    {
        if (!is_null($filterChainName) && !isset($this->filterChains[$filterChainName])) {
            $this->filterChains[$filterChainName] = new MDB2_TableBrowser_FilterManager($this->validator);
        } else {
            throw new MDB2_TableBrowser_ParameterException("Invalid filterChainName $filterChainName");
        }
    }
    /**
     * Deletes a new filter chain
     *
     * @param string $filterChainName The name of the filterChain
     * 
     * @throws MDB2_TableBrowser_ParameterException
     * 
     * @return void
     */
    public function deleteFilterChain($filterChainName)
    {
        if (!is_null($filterChainName) && $filterChainName != self::DEFAULT_CHAIN_NAME && isset($this->filterChains[$filterChainName])) {
            unset($this->filterChains[$filterChainName]);
            if ($this->currentFilterChain == $filterChainName) {
                $filterChainName = self::DEFAULT_CHAIN_NAME;
            }
        } else {
            throw new MDB2_TableBrowser_ParameterException("Invalid filterChainName $filterChainName");
        }
    }
    /**
     * Switches the selected filter chain
     *
     * @param string $filterChainName The name of the filterChain
     * 
     * @throws MDB2_TableBrowser_ParameterException
     * 
     * @return void
     */
    public function selectFilterChain($filterChainName = null)
    {
        if (is_null($filterChainName)) {
            $this->currentFilterChain = self::DEFAULT_CHAIN_NAME;
        } elseif (isset($this->filterChains[$filterChainName])) {
            $this->currentFilterChain = $filterChainName;
        } else {
            throw new MDB2_TableBrowser_ParameterException("Invalid filterChainName $filterChainName", null);
        }
    }
    /**
     * Resets all filter chains
     * 
     * @return void
     *
     */
    public function resetAllFilters()
    {
        foreach ($this->filterChains as $filterObj) {
            $filterObj->resetFilters();
        }
    }
    /**
     * Combines all the filter chains and generates the complete where clause
     *
     * @return string the complete where clauses
     */
    public function generateSQL()
    {
        $sqlClauses = array();
        foreach ($this->filterChains as $filter) {
            $sqlClause = $filter->generateSQL();
            if ($sqlClause != '') {
                $sqlClauses[] = '(' .$sqlClause . ')';
            }
        }
        $whereClause = join(' OR ', $sqlClauses);
        return $whereClause;
    }
}
