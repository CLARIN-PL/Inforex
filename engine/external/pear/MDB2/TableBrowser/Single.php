<?php
/**
 * This file contains a class that implements the SingleTableBrowser interface
 * 
 * PHP version 5
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   SVN:<svn_id>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/DBException.php
 */
require_once 'MDB2/TableBrowser/Interfaces.php';
require_once 'MDB2/TableBrowser/ParameterException.php';
require_once 'MDB2/TableBrowser/DBException.php';
require_once 'MDB2/TableBrowser/TableValidator.php';
require_once 'MDB2/TableBrowser/FilterManager.php';
require_once 'MDB2/TableBrowser/ColumnManager.php';
require_once 'MDB2/TableBrowser/MultipleFliterManager.php';


/**
 * This class implements the SingleTableBrowser interface
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/ColumnManager.php
 * @see       SingleTableBrowser
 */
class MDB2_TableBrowser_Single implements MDB2_TableBrowser_InterfaceSingleTableBrowser
{
    //Stores and MDB2 instance
    protected $mdb2;
    //Stores the table name
    protected $tableName;
    //Stores the column name that is primary key 
    protected $primary_key;
    //Stores the last excecuted sql statement
    protected $lastSQL;
    
    //Flag to turn/off duplicate result returning
    protected $distinct = false;
    
    //Stores an MDB2_TableBrowser_tableValidator reference
    protected $validator;
    //Stores an MDB2_TableBrowser_MultipleFilterManager reference
    protected $filterManager;
    //Stores the other filter chains
    protected $otherFilters = array();
    //Stores an MDB2_TableBrowser_ColumnManager reference
    protected $columnManager;
    //Stores an MDB2_TableBrowser_OrderByManager reference
    protected $orderByManager;
    //Stores an MDB2_TableBrowser_GroupByManager reference
    protected $groupByManager;
    
    //Array containing refrences to all external methods that will be dispatched using __call
    private $_externalMethods;
    
    /**
     * Constructor
     *
     * @param MDB2    $mdb2          MDB2 Object
     * @param string  $tableName     The table name
     * @param string  $primary_key   The primary key
     * @param boolean $caseSensitive Flag for case sensitivity
     */
    public function __construct(MDB2_Driver_Common $mdb2, $tableName, $primary_key, $caseSensitive = false)
    {
        $this->mdb2        = $mdb2;
        $this->tableName   = $tableName;
        $this->primary_key = $primary_key;
        
        // Load the Extended module
        $this->mdb2->loadModule('Extended', null, false);
        
        $this->validator      = new MDB2_TableBrowser_TableValidator($mdb2, $tableName, $caseSensitive);
        $this->filterManager  = new MDB2_TableBrowser_MultipleFilterManager($this->validator);
        $this->columnManager  = new MDB2_TableBrowser_ColumnManager($this->validator);
        $this->orderByManager = new MDB2_TableBrowser_OrderByManager($this->validator);
        $this->groupByManager = new MDB2_TableBrowser_GroupByManager($this->validator);
        
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
                                    'selectColumns'      => $this->columnManager,
                                    'resetSelectColumns' => $this->columnManager,
                                    'setColumnAlias'     => $this->columnManager,
                                    'resetColumAliases'  => $this->columnManager,
                                    'removeColumnAlias'  => $this->columnManager,
                                    'addCustomColumn'    => $this->columnManager,
                                    'removeCustomColumn' => $this->columnManager,
        
                                    'addFilter'         => $this->filterManager,
                                    'removeFilter'      => $this->filterManager,
                                    'resetFilters'      => $this->filterManager,
                                    'addCustomFilter'   => $this->filterManager,
                                    'createFilterChain' => $this->filterManager,
                                    'deleteFilterChain' => $this->filterManager,
                                    'selectFilterChain' => $this->filterManager,
                                    'resetAllFilters'   => $this->filterManager,
        
                                    'getColumns'   => $this->validator,
                                            
                                    'setGroupBy'   => $this->groupByManager,
                                    'resetGroupBy' => $this->groupByManager,
                                            
                                    'setOrderBy'   => $this->orderByManager,
                                    'resetOrderBy' => $this->orderByManager);
    }
    /**
     * Dispatch method to call methods defined in the supporting classes
     *
     * @param string $method The method name
     * @param array  $args   The method parameters
     * 
     * @return mixed the dispatch method's return
     */
    public function __call($method, $args)
    {
        if (isset($this->_externalMethods[$method])) {
            $obj = $this->_externalMethods[$method];
            //return call_user_method($method, $obj, &$args);
            return call_user_func_array(array($obj, $method), array_merge(array($obj), $args));
        }
        //Unknown methods throw exception
        throw new MDB2_TableBrowser_ParameterException("Unknown Method $method", $args);
    }
    /**
     * Sets browser to return only unique results
     * 
     * @return void
     *
     */
    public function setDistinct()
    {
        $this->distinct = true;
    }
    /**
     * Sets the browser to return duplicate results
     * 
     * @return void
     *
     */
    public function unsetDistinct()
    {
        $this->distinct = false;
    }
    /**
     * Returns the different values a table column has. It also uses the 
     * currently defined filters to constrain the results returned.
     *
     * @param string  $columnName The column name
     * @param integer $limit      Limit the number of results returned
     * @param integer $offset     Offset
     * 
     * @return array The column value
     */
    public function getColumnValues($columnName, $limit = MDB2_TableBrowser_InterfaceSingleTableBrowser::DEFAULT_MAX_ROWS, $offset=0)
    {
        //Get the real column name
        $columnName = $this->columnManager->getRealColumnName($columnName);
        //Create a new column selector so as not to interfere with the current column selections
        $columnManager = new MDB2_TableBrowser_ColumnManager($this->validator);
        $sql           = 'SELECT DISTINCT ';
        $columnManager->selectColumns(array($columnName));
        $sql .= $columnManager->generateSQL() . ' FROM ' . $this->tableName;
        //Use the existing filters set by the user
        $whereClause = $this->filterManager->generateSQL();
        if (!empty($whereClause)) {
            $sql .= ' WHERE ' . $whereClause;
        }
        return $this->query($sql, $limit, $offset);
    }
    /**
     * Identical to calling getRows, but instead of excecuting the query
     * it returns the sql that would have been generated from a getRows call.
     * 
     * This method is useful for debugging or building complex sql constructs
     * that involve sub-selects.
     *
     * @return string
     */
    public function getRowsSQL()
    {
        if ($this->distinct) {
            $sql = 'SELECT DISTINCT';
        } else {
            $sql = 'SELECT';
        }
        $sql .= ' ' . $this->columnManager->generateSQL();
        $sql .= ' FROM ' . $this->tableName;
        
        $whereClause   = $this->filterManager->generateSQL();
        $orderByClause = $this->orderByManager->generateSQL();
        $groupByClause = $this->groupByManager->generateSQL();
        
        if (!empty($whereClause)) {
            $sql .= ' WHERE ' . $whereClause;
        }
        if (!empty($groupByClause)) {
            $sql .= ' GROUP BY ' . $groupByClause;
        }
        if (!empty($orderByClause)) {
            $sql .= ' ORDER BY ' . $orderByClause;
        }
        return $sql;
    }
    /**
     * Reterives the rows in the table. It uses the currently defined filters
     * to constrain the results returned.
     *
     * @param int $limit  Limit results
     * @param int $offset Offset
     * 
     * @return an MDB2_Result object
     */
    public function getRows($limit = MDB2_TableBrowser_InterfaceSingleTableBrowser::DEFAULT_MAX_ROWS, $offset=0)
    {
        $sql = $this->getRowsSQL();
        $this->mdb2->setLimit($limit, $offset);
        return $this->query($sql);
    }
    /**
     * Returns a single row in the table with the primary key value specified.
     * This method ignores the currently defined filters.
     * 
     * @param mixed $rowID The primary_key value
     * 
     * @return array The row data
     */
    public function getRow($rowID)
    {
        $filter = new MDB2_TableBrowser_FilterManager($this->validator);
        $filter->addFilter('PRIMARY_KEY', $this->primary_key, '=', $rowID);
        $sql  = 'SELECT ' . $this->columnManager->generateSQL();
        $sql .= ' FROM ' . $this->tableName;
        $sql .= ' WHERE ' . $filter->generateSQL();
        $this->mdb2->setLimit(1, 0);
        $result = $this->query($sql);
        return $result->fetchRow(MDB2_FETCHMODE_ASSOC);
    }
    /**
     * Updates a row
     *
     * @param mixed $rowID   The primary key value
     * @param array $rowData The new row values 
     * 
     * @throws MDB2_TableBrowser_ParameterException
     * @return int The number of affected rows (0 or 1)
     */
    public function updateRow($rowID, $rowData)
    {
        $sql          = 'UPDATE ' . $this->tableName . ' SET ';
        $tableColumns = $this->validator->getColumns();
        
        //Generate the update columns sql
        $updateColArray = array();
        foreach ($rowData as $column => $value) {
            //Get the real column name (may be an alias)
            $realColumn = $this->columnManager->getRealColumnName($column);
            if ($this->validator->isValidColumn($realColumn)) {
                $escapedColName   = $this->validator->quoteIdentifier($realColumn);
                $colVal           = $this->validator->quote($rowData[$column]);
                $updateColArray[] = $escapedColName . '= ' . $colVal;
            } else {
                throw new MDB2_TableBrowser_ParameterException("Invalid column $realColumn", $rowData);
            }
        }
        $sql .= join(',', $updateColArray);
        
        //Generate the where clause
        $filter = new MDB2_TableBrowser_FilterManager($this->validator);
        $filter->addFilter('PRIMARY_KEY', $this->primary_key, '=', $rowID);
        $sql .= ' WHERE ' . $filter->generateSQL();
        $this->mdb2->setLimit(1);
        return $this->execute($sql);
    }
    /**
     * Deletes a single row with the specified rowId.
     *
     * @param mixed $rowID The primary key value
     * 
     * @return int The number of affected rows (0 or 1)
     */
    public function deleteRow($rowID)
    {
        $filter = new MDB2_TableBrowser_FilterManager($this->validator);
        $filter->addFilter('PRIMARY_KEY', $this->primary_key, '=', $rowID);
        $sql  = 'DELETE ';
        $sql .= ' FROM ' . $this->tableName;
        $sql .= ' WHERE ' . $filter->generateSQL();
        $this->mdb2->setLimit(1, 0);
        return $this->execute($sql);
    }
    /**
     * Inserts a row
     *
     * @param array $rowData The new row data
     * 
     * @return int The number of affected rows (0 or 1)
     */
    public function insertRow($rowData)
    {
        $data    = array();
        $columns = array();
        foreach ($rowData as $columnName => $value) {
            $realColumn = $this->columnManager->getRealColumnName($columnName);
            if ($this->validator->isValidColumn($realColumn)) {
                $columns[] = $realColumn;
                $data[]    = $value;
            }
        }
        return $this->insertRows($columns, array($data));
    }
    /**
     * Inserts multiple rows to the table
     *
     * @param array $columns The list of colums in the data
     * @param array $rowData The array containing the row data
     * 
     * @return int The number of inserted rows
     */
    public function insertRows($columns, $rowData)
    {
        $tblColumns   = array();
        $placeHolders = array();
        foreach ($columns as $col) {
            $realColumn = $this->columnManager->getRealColumnName($col);
            if ($this->validator->isValidColumn($realColumn)) {
                $tblColumns[]   = $this->validator->quoteIdentifier($realColumn);
                $placeHolders[] = '?';
            } else {
                $args = func_get_args();
                throw new MDB2_TableBrowser_ParameterException("Invalid column $col", $args);
            }
        }
        $sql = 'INSERT INTO ' . $this->tableName . '(' . join(',', $tblColumns) . ') VALUES ('. join(',', $placeHolders) . ')';
        
        $this->lastSQL = $sql;
        $sth           = $this->mdb2->prepare($sql);
        if (PEAR::isError($sth)) {
            throw new MDB2_TableBrowser_DBException($sth);
        }
        $affected = $this->mdb2->extended->executeMultiple($sth, $rowData);
        if (PEAR::isError($affected)) {
            throw new MDB2_TableBrowser_DBException($affected);
        }
        return $affected;
        
    }
    /**
     * Excecutes a select query
     *
     * @param string $sql A select sql statement
     * 
     * @throws MDB2_TableBrowser_DBException If the query failed
     * 
     * @return MDB2_Result object
     */
    protected function query($sql)
    {
        $this->lastSQL = $sql;
        $result        = $this->mdb2->query($sql);
        if (PEAR::isError($result)) {
            print $sql;
            throw new MDB2_TableBrowser_DBException($result);
        }
        return $result;
    }
    /**
     * Excecutes an sql statement
     *
     * @param string $sql The sql statement
     * 
     * @throws MDB2_TableBrowser_DBException If the query failed
     * 
     * @return int the number of affected rows
     */
    protected function execute($sql)
    {
        $this->lastSQL = $sql;
        $affected      = $this->mdb2->exec($sql);
        if (PEAR::isError($affected)) {
            throw new MDB2_TableBrowser_DBException($affected);
        }
        return $affected;
    }
    /**
     * Returns the last sql statement
     *
     * @return string
     */
    public function getLastSQL()
    {
        return $this->lastSQL;
    }
}
