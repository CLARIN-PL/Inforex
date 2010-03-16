<?php
/**
 * This file contains a class that implements the whereClauseGenerator interface
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

require_once 'Interfaces.php';
require_once 'MDB2.php';
require_once 'DBException.php';

/**
 * This class implements the whereClauseGenerator interface
 * 
 * It is used by other classes to create and manage the where clause of an
 * SQL statement. As such, it's primary purpose is to add/remove filters which
 * are expressions that evaluate to true or false.
 * 
 * These filters together form a filter-chain. A row is described as matching 
 * if each filter in the chain is evaluated as true.
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/ColumnManager.php
 */
class MDB2_TableBrowser_FilterManager implements MDB2_TableBrowser_InterfaceWhereClauseGenerator
{
    protected $validator = null;
    protected $filters   = array();
    
    /**
     * Constructor
     *
     * @param tableValidator &$validator A refrence to an object that implements the tableValidator interface
     */
    public function __construct(MDB2_TableBrowser_InterfaceTableValidator &$validator)
    {
        $this->validator = $validator;
    }
    /**
     * Adds a filter to the filter chain
     *
     * A filter is an expression of the form columnName = "some value'. It can
     * also be multivalued eg: (columnName = "value1" OR columnName="value2")
     *
     * @param string $filterName The filter name
     * @param string $columnName The column name
     * @param string $operator   The operator eg: '=', '<>', '>=', etc.
     * @param mixed  $value      If string, then a single filter is being added
     *                           eg: "animal = 'cat'"
     *                           If array, then a multivalue filter is being added
     *                           eg: "animal is one of 'cat','dog', or 'fish'"
     * 
     * @return void
     *
     */
    public function addFilter($filterName, $columnName, $operator, $value)
    {
        $this->validateExpression($columnName, $operator, $value);
        if (is_array($value)) {
            $filter = array();;
            foreach ($value as $single_val) {
                $filter[] = $this->createSQLFilter($columnName, $operator, $single_val, $wrap_string);
            }
            $this->filters[$filterName] = '(' . join($filter, ' OR ') . ')';
        } else {
            $this->filters[$filterName] = $this->createSQLFilter($columnName, $operator, $value);
        }
    }
    /**
     * Creates the actual sql fragment that forms the filter 
     *
     * @param string $columnName The column name
     * @param string $operator   The operator
     * @param string $value      The column value
     * 
     * @return string
     */
    protected function createSQLFilter($columnName, $operator, $value)
    {
        return $this->validator->quoteIdentifier($columnName) .' '. $operator . ' '. $this->validator->quote($value);
    }
    /**
     * Clears the current filter chain
     * 
     * @return void
     */
    public function resetFilters()
    {
        $this->filters = array();
    }
    /**
     * Removes a filter from the filter chain
     *
     * @param string $filterName The filter name
     * 
     * @return void
     */
    public function removeFilter($filterName)
    {
        unset($this->filters[$filterName]);
    }
    /**
     * Adds a custom filter. A custom filter is an sql fragment that is added
     * to the where clause of an sql statement. Like any other filter it must
     * evaluate to true/false
     *
     * @param string $filterName   The filter name
     * @param string $sqlFrangment The sql fragment
     * 
     * @return void
     */
    public function addCustomFilter($filterName, $sqlFrangment)
    {
        $this->filters[$filterName] = $sqlFrangment;
    }
    /**
     * Converts the current filter chain into a string containing the where
     * clause
     *
     * @return string
     */
    public function generateSQL()
    {
        if (count($this->filters)) {
            return '(' . join(array_values($this->filters), ' AND ') . ')';
        }
        return null;
    }
    /**
     * Checks to see if the parameters will form a proper filter expression. It
     * uses the tableValidator object to do this.
     *
     * @param string $columnName The name of table column
     * @param string $operator   The operator
     * @param string $value      The value
     * 
     * @throws MDB2_TableBrowser_ParameterException if one of the parameters is invalid
     * 
     * @return void
     */
    public function validateExpression($columnName, $operator, $value)
    {
        $args = func_get_args();
        if (!$this->validator->isValidColumn($columnName)) {
            throw new MDB2_TableBrowser_ParameterException("Invalid column '$columnName'", $args);
        }
        if (!$this->validator->isValidOperator($operator)) {
            throw new MDB2_TableBrowser_ParameterException("Invalid operator '$operator'", $args);
        }
        if (!$this->validator->isValidData($columnName, $value)) {
            throw new MDB2_TableBrowser_ParameterException("Data type misatch for column '$columnName', value '$value'", func_get_args());
        }
    }
}
?>