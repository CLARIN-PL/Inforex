<?php
/**
 * This file contains a class that implements the selectClauseGenerator interface
 * 
 * PHP version 5
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   SVN:<svn_id>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/ColumnManager.php
 */


/**
 * This class implements the selectClauseGenerator interface
 * 
 * It is used by other classes to create and manage the select clause of an
 * SQL statements. As such, it's primary purpose is to select columns and
 * assign labels to them.
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableBrowser/ColumnManager.php
 */
class MDB2_TableBrowser_ColumnManager implements MDB2_TableBrowser_InterfaceSelectClauseGenerator
{
    
    //Stores a reference to an object that implements 
    protected $validator = null;
    //Stores the selected columns
    protected $columns = array();
    //Stores the custom columns
    protected $customColumns = array();
    //Stores aliases to columns
    protected $columnsAliases = array();
    //Stores a reverse lookup for column aliases
    protected $columnsAliasesReverseLookup = array();
    
    /**
     * Constructor
     *
     * @param tableValidator &$validator A refrence to an object that implements the tableValidator interface
     */
    public function __construct(MDB2_TableBrowser_InterfaceTableValidator &$validator)
    {
        $this->validator = $validator;
        $this->columns   = $this->validator->getColumns();
    }
    /**
     * Selects the columns that will be included in the select clause
     *
     * @param array $colNames The columns to be included in the select clause
     * 
     * @throws MDB2_TableBrowser_ParameterException if column is invalid
     * @return void
     */
    public function selectColumns($colNames)
    {
        $this->columns = array();
        $args          = func_get_args();
        foreach ($colNames as $colName) {
            if (!$this->validator->isValidColumn($colName)) {
                throw new MDB2_TableBrowser_ParameterException("Invalid column '$colName'", $args);
            }
            $this->columns[] = $colName;
        }
    }
    /**
     * Resets the column selections
     * 
     * @return void
     *
     */
    public function resetSelectColumns()
    {
        $this->columns = $this->validator->getColumns();
    }
    /**
     * Sets an alias for the sepecified column
     *
     * @param string $colName The column name
     * @param string $alias   The column alias
     * 
     * @return void
     * @throws MDB2_TableBrowser_ParameterException if column is invalid
     */
    public function setColumnAlias($colName, $alias)
    {
        $args = func_get_args();
        if (!$this->validator->isValidColumn($colName)) {
            throw new MDB2_TableBrowser_ParameterException("Invalid column '$colName'", $args);
        }
        $formattedColName = $this->validator->formatIdentifier($colName);
        $formattedAlias   = $this->validator->formatIdentifier($alias);
        
        $this->columnsAliases[$formattedColName]            = $alias;
        $this->columnsAliasesReverseLookup[$formattedAlias] = $formattedColName;
    }
    /**
     * Removes all column aliases
     *
     * @return void
     */
    public function resetColumAliases()
    {
        $this->columnsAliases              = array();
        $this->columnsAliasesReverseLookup = array();
    }
    /**
     * Removes a column alias
     *
     * @param string $colName The column Name
     * 
     * @return void
     */
    public function removeColumnAlias($colName)
    {
        $formattedColName = $this->validator->formatIdentifier($colName);
        if (isset($this->columnsAliases[$formattedColName])) {
            $alias          = $this->columnsAliases[$formattedColName];
            $formattedAlias = $this->validator->formatIdentifier($alias);
            unset($this->columnsAliases[$formattedColName]);
            unset($this->columnsAliasesReverseLookup[$formattedAlias]);
        }
    }
    /**
     * Gets a column alias
     *
     * @param string $colName The column Name
     * 
     * @return string The column alias
     */
    public function getColumnAlias($colName)
    {
        $formattedColName = $this->validator->formatIdentifier($colName);
        if (isset($this->columnsAliases[$formattedColName])) {
            return $this->columnsAliases[$formattedColName];
        }
        return null;
    }
    /**
     * Returns the actual column name given an alias. It will simply
     * return the columnName if it is being passed the columnName instead of
     * an alias.
     *
     * @param string $alias The column alias
     * 
     * @return string The real column name
     */
    public function getRealColumnName($alias)
    {
        $formattedAlias = $this->validator->formatIdentifier($alias);
        if (isset($this->columnsAliasesReverseLookup[$formattedAlias])) {
            return $this->columnsAliasesReverseLookup[$formattedAlias];
        } elseif ($this->validator->isValidColumn($formattedAlias)) {
            return $this->validator->formatIdentifier($formattedAlias);
        }
        return null;
    }
    /**
     * Adds a custom column to the table_browser. Used for columns that are a
     * function and not a tableColumn
     *
     * @param string $colName The column name (case sensitive)
     * @param string $alias   An alias for the column(optional)
     * 
     * @return void
     */
    public function addCustomColumn($colName, $alias=null)
    {
        $this->customColumns[$colName] = $colName;
        if (!is_null($alias)) {
            $this->columnsAliases[$colName] = $alias;
        }
    }
    /**
     * Removes a custom column
     *
     * @param string $colName The column (case sensitive)
     * 
     * @return void
     */
    public function removeCustomColumn($colName)
    {
        unset($this->columnsAliases[$colName]);
        unset($this->customColumns[$colName]);
    }
    /**
     * Generates the select clause of an sql statement
     *
     * @return string the select clause
     */
    public function generateSQL()
    {
        $selectClause = '';
        $allColumns   = array_merge($this->columns, $this->customColumns);
        $columns      = array();
        foreach ($allColumns as $column) {
            if (array_key_exists($column, $this->customColumns)) {
                $columnName        = $column;
                $cleanedColumnName = $columnName;
            } else {
                $columnName        = $this->validator->formatIdentifier($column);
                $cleanedColumnName = $this->validator->quoteIdentifier($column);
            }
            if (array_key_exists($columnName, $this->columnsAliases)) {
                $alias     = $this->validator->quoteIdentifier($this->columnsAliases[$columnName]); 
                $columns[] = $cleanedColumnName . ' AS ' . $alias;
            } else {
                $columns[] = $cleanedColumnName;
            }
            $selectClause = join(',', $columns);
        }
        return $selectClause;
    }
}
?>