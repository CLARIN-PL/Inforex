<?php
/**
 * This file contains the interfaces used in the MDB2_TableBrowser library
 * 
 * PHP version 5
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   SVN:<svn_id>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/Interfaces.php
 */

/**
 * This interface defines a behaviour that includes generating all or part of
 * an sql statment.
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/Interfaces.php
 */
interface MDB2_TableBrowser_InterfaceSQLGenerator
{
    /**
     * Producers part or all of an sql statement
     * 
     * @return string The sql statement/fragment
     */
    public function generateSQL();
}

/**
 * This interface defines a behaviour that includes generating all or part of
 * the 'where' clause in an sql statement. The where clause is built using
 * filters where each filter is a single expression.
 *
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/Interfaces.php
 */
interface MDB2_TableBrowser_InterfaceWhereClauseGenerator extends MDB2_TableBrowser_InterfaceSQLGenerator
{
    /**
     * Adds a filter to the filter chain
     *
     * A filter is an expression of the form columnName = "some value'. It can
     * also be multivalued eg: (columnName = "value1" OR columnName="value2")
     *
     * @param string $filterName The filter name
     * @param string $columnName The column name
     * @param string $operator   The operator eg: '=', '<>', etc.
     * @param mixed  $value      If string, then a single filter is being added
     *                           eg: "animal = 'cat'"
     *                           If array, then a multivalue filter is being added
     *                           eg: "animal is one of 'cat','dog', or 'fish'"
     * 
     * @return void
     *
     */
    public function addFilter($filterName, $columnName, $operator, $value);
    /**
     * Clears the current filter chain
     * 
     * @return void
     */
    public function resetFilters();
    /**
     * Removes a filter from the filter chain
     *
     * @param string $filterName The filter name
     * 
     * @return void
     */
    public function removeFilter($filterName);
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
    public function addCustomFilter($filterName, $sqlFrangment);
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
    public function validateExpression($columnName, $operator, $value);
}
/**
 * This interface defines a behaviour that includes generating all or part of
 * the 'where' clause in an sql statement. The where clause is built using
 * filters where each filter is a single expression.
 *
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/Interfaces.php
 */
interface MDB2_TableBrowser_InterfaceMultipleWhereClauseGenerator extends MDB2_TableBrowser_InterfaceSQLGenerator
{
    const DEFAULT_CHAIN_NAME = '_MAIN';
    /**
     * Creates a new filter chain
     *
     * @param string $filterChainName The name of the filterChain
     * 
     * @return void
     */
    public function createFilterChain($filterChainName);
    /**
     * Deletes a new filter chain
     *
     * @param string $filterChainName The name of the filterChain
     * 
     * @return void
     */
    public function deleteFilterChain($filterChainName);
    /**
     * Switches the selected filter chain
     *
     * @param string $filterChainName The name of the filterChain
     * 
     * @return void
     */
    public function selectFilterChain($filterChainName = null);
    /**
     * Resets all filter chains
     * 
     * @return void
     *
     */
    public function resetAllFilters();
}
/**
 * This interface defines a behaviour that includes generating all or part of
 * the 'select' clause in an sql statement
 *
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/Interfaces.php
 */
interface MDB2_TableBrowser_InterfaceSelectClauseGenerator extends MDB2_TableBrowser_InterfaceSQLGenerator
{
    /**
     * Selects the columns that will be included in the select clause
     *
     * @param array $colNames The columns to be included in the select clause
     * 
     * @throws MDB2_TableBrowser_ParameterException if column is invalid
     * @return void
     */
    public function selectColumns($colNames);
    /**
     * Resets the column selections
     * 
     * @return void
     */
    public function resetSelectColumns();
    /**
     * Sets an alias for the sepecified column
     *
     * @param string $colName The column name
     * @param string $alias   The column alias
     * 
     * @return void
     * @throws MDB2_TableBrowser_ParameterException if column is invalid
     */
    public function setColumnAlias($colName, $alias);
    /**
     * Removes all column aliases
     *
     * @return void
     */
    public function resetColumAliases();
    /**
     * Removes a column alias
     *
     * @param string $colName The column Name
     * 
     * @return void
     */
    public function removeColumnAlias($colName);
    /**
     * Gets a column alias
     *
     * @param string $colName The column Name
     * 
     * @return string The column alias
     */
    public function getColumnAlias($colName);
    /**
     * Returns the actual column name given an alias. It will simply
     * return the columnName if it is being passed the columnName instead of
     * an alias.
     *
     * @param string $alias The column alias
     * 
     * @return string The real column name
     */
    public function getRealColumnName($alias);
    /**
     * Adds a custom column to the table_browser. Used for columns that are a
     * function and not a tableColumn
     *
     * @param string $colName The column (case sensitive)
     * @param string $alias   An alias for the column(optional)
     * 
     * @return void
     */
    public function addCustomColumn($colName, $alias=null);
    /**
     * Removes a custom column
     *
     * @param string $colName The column (case sensitive)
     * 
     * @return void
     */
    public function removeCustomColumn($colName);
}

/**
 * This interface defines a set of methods that use the table definition
 * to help insure that the generated sql query will be valid
 *
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/Interfaces.php
 */
interface MDB2_TableBrowser_InterfaceTableValidator
{
    /**
     * Returns all the columns of the table, in the order that they are
     * defined in the table
     *
     * @return array the column names
     */
    public function getColumns();
    /**
     * Gets a properly formated Insert sql statment for the table.
     *
     * @return string
     */
    public function getInsertSQL();
    /**
     * Checks to see if a column exists in the table definition
     *
     * @param string $columnName The column name
     * 
     * @return boolean
     */
    public function isValidColumn($columnName);
    /**
     * Checks to see if a row is valid given the table definition
     * 
     * @param array $rowData The row data
     * 
     * @return unknown
     */
    public function isValidRow($rowData);
    /**
     * Checks to see if a given column value is valid given the table definition
     *
     * @param string $colName     The column name
     * @param string $columnValue The column value
     * 
     * @return boolean
     */
    public function isValidData($colName, $columnValue);
    /**
     * Checks to see if an operator is valid
     *
     * @param string $op The operator eg: '=', '>='
     * 
     * @return boolean
     */
    public function isValidOperator($op);
    /**
     * Escapes/Quotes a column name (uses mdb2's quoteIdentifier)
     *
     * @param string $columnName The column name
     * 
     * @return string
     */
    public function quoteIdentifier($columnName);
    /**
     * Escapes/Quotes a column value (uses mdb2's quote)
     *
     * @param string $data The column value
     * 
     * @return string
     */
    public function quote($data);
}
/**
 * This interface defines a behaviour that includes generating all or part of
 * the 'ORDER BY' clause in an sql statement
 *
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/Interfaces.php
 */
interface MDB2_TableBrowser_InterfaceOrderByClauseGenerator extends MDB2_TableBrowser_InterfaceSQLGenerator
{
    /**
     * Order results. Multiple columns can be added
     *
     * @param string $colName   The column name
     * @param string $direction The direction either ASC or DESC, (optional)
     * 
     * @return void
     */
    public function setOrderBy($colName, $direction = null);
    /**
     * Clears all sorting columns
     *
     * @return void
     */
    public function resetOrderBy();
}

/**
 * This interface defines a behaviour that includes generating all or part of
 * the 'GROUP BY' clause in an sql statement
 *
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/Interfaces.php
 */
interface MDB2_TableBrowser_InterfaceGroupByClauseGenerator extends MDB2_TableBrowser_InterfaceSQLGenerator
{
    /**
     * Groups rows. Multiple groupBys be can specified
     *
     * @param string $colName The column name
     * 
     * @return void
     */
    public function setGroupBy($colName);
    /**
     * Clears all sorting columns
     *
     * @return void
     */
    public function resetGroupBy();
}

/**
 * This interface defines the set of methods(operations) that can be performed
 * on a table by the tableBrowser
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/Interfaces.php
 */
interface MDB2_TableBrowser_InterfaceSingleTableBrowser
{
    const DEFAULT_MAX_ROWS = 100;
    
    /**
     * Sets browser to return only unique results
     * 
     * @return void
     *
     */
    public function setDistinct();
    /**
     * Sets the browser to return duplicate results
     * 
     * @return void
     *
     */
    public function unsetDistinct();
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
    public function getColumnValues($columnName, $limit = DEFAULT_MAX_ROWS, $offset = 0);
    /**
     * Identical to calling getRows, but instead of excecuting the query
     * it returns the sql that would have been generated from a getRows call.
     * 
     * This method is useful for debugging and building complex sql constructs
     * that involve sub-selects.
     * 
     * @return string
     *
     */
    public function getRowsSQL();
    /**
     * Reterives the rows in the table. It uses the currently defined filters
     * to constrain the results returned.
     *
     * @param int $limit  Limit results
     * @param int $offset Offset
     * 
     * @return an MDB2_Result object
     */
    public function getRows($limit = DEFAULT_MAX_ROWS, $offset=0);
    /**
     * Returns a single row in the table with the primary key value specified.
     * This method ignores the currently defined filters.
     * 
     * @param mixed $rowID The primary_key value
     * 
     * @return array The row data
     */
    public function getRow($rowID);
    /**
     * Updates a row
     *
     * @param mixed $rowID   The primary key value
     * @param array $rowData The new row values 
     * 
     * @return int The number of affected rows (0 or 1)
     */
    public function updateRow($rowID, $rowData);
    /**
     * Deletes a single row with the specified rowId.
     *
     * @param mixed $rowID The primary key value
     * 
     * @return int The number of affected rows (0 or 1)
     */
    public function deleteRow($rowID);
    /**
     * Inserts a row
     *
     * @param array $rowData The new row data
     * 
     * @return int The number of affected rows (0 or 1)
     */
    public function insertRow($rowData);
    /**
     * Inserts multiple rows to the table
     *
     * @param array $columns The list of colums in the data
     * @param array $rowData The array containing the row data
     * 
     * @return int The number of inserted rows
     */
    public function insertRows($columns, $rowData);
    /**
     * Returns the last sql statement
     *
     * @return string
     */
    public function getLastSQL();

}
/**
 * This interface defines the set of methods(operations) that can be performed
 * by the multi-tableBrowser. This interface allows for the addtion of multiple
 * tables to the tableBrowser. Tables are added one at a time.
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/Interfaces.php
 */
interface MDB2_TableBrowser_InterfaceMultiTableBrowser
{
    const DEFAULT_MAX_ROWS = 100;
    
    /**
     * Sets browser to return only unique results
     * 
     * @return void
     *
     */
    public function setDistinct();
    /**
     * Sets the browser to return duplicate results
     * 
     * @return void
     *
     */
    public function unsetDistinct();
    /**
     * Adds a table to the multiTable browser. This method will actually add
     * one or both tables. Internally they are created as single tableBrowsers
     * and the columns $table1Column & $table2Column are added to the join
     * expression.
     * 
     * Joins are added one at a time and are specified in the sql in the order 
     * they are created.
     *
     * @param string $table1       The table1 name
     * @param string $table1Column The table1 column
     * @param string $table2       The table2 name
     * @param string $table2Column The table2 column
     * @param string $joinType     The join type (optional)
     * 
     * @return void
     */
    public function addTable($table1, $table1Column, $table2, $table2Column, $joinType = 'JOIN');
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
    public function getColumnValues($columnName, $limit = DEFAULT_MAX_ROWS, $offset = 0);
    /**
     * Reterives the rows in the table. It uses the currently defined filters
     * to constrain the results returned.
     *
     * @param int $limit  Limit results
     * @param int $offset Offset
     * 
     * @return an MDB2_Result object
     */
    public function getRows($limit = DEFAULT_MAX_ROWS, $offset=0);
    /**
     * Returns the last sql statement
     *
     * @return string
     */
    public function getLastSQL();
}
?>