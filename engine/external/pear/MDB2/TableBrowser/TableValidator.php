<?php
/**
 * This file contains a class that implements the TableValidator interface
 * 
 * PHP version 5
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   SVN:<svn_id>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableValidator.php
 */

/**
 * This class implements the TableValidator interface
 * 
 * This class provides methods that provide information about a table structure
 * and columns. It is used by other classes to insure paramaters passed on
 * by the user make sense in light of a table's definition
 * 
 * @category  Database
 * @package   MDB2_TableBrowser
 * @author    Isaac Tewolde, <isaac@ticklespace.com>
 * @copyright 2007-2012 Isaac Tewolde
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL v3.0
 * @version   Release: <package_version>
 * @link      http://code.google.com/p/mdb2tablebrowser/source/browse/trunk/MDB2_TableBrowser/TableValidator.php
 *
 */
class MDB2_TableBrowser_TableValidator implements MDB2_TableBrowser_InterfaceTableValidator
{
    //Refrence to the tableRegisty. Used to preven reloading of table info
    protected static $tableRegistry = array();
    
    //Stores the table name
    protected $tableName = null;
    //Stores a refrence to an mdb2 object
    protected $mdb2Object = null;
    
    //Flag to set case sensitivity for table columns
    protected $caseSensitiveIdentifiers = false;
    
    
    /**
     * Constructor
     *
     * @param MDB2_Driver_Common &$mdbref       Refrence to an MDB2 connection
     * @param string             $tableName     The table name
     * @param boolean            $caseSensitive flag to make the library case sensitive
     */
    public function __construct(MDB2_Driver_Common &$mdbref, $tableName, $caseSensitive = false)
    {
        $this->mdb2Object = $mdbref;
        $this->tableName  = $tableName;
        $this->loadTableDefinition();
        $this->caseSensitiveIdentifiers = $caseSensitive;
    }
    /**
     * formats the columnName to be in a format used internally. Column names
     * are by default stored in a cases insensitive manner.
     *
     * @param string $name The identifier
     * 
     * @return string The formated name
     */
    public function formatIdentifier($name)
    {
        return strtoupper($name);
    }
    /**
     * Loads a table definition and stores it in the class variable self::$tableRegistry
     *
     * @return array structure containing the table definition
     * @throws MDB2_TableBrowser_DBException if the table is invalid or inaccessable
     */
    protected function loadTableDefinition()
    {
        //TODO: Need to check database type as not all use case insensitive table/column names
        $tableName = $this->formatIdentifier($this->tableName);
        if (array_key_exists($tableName, self::$tableRegistry)) {
            $this->currentTable = self::$tableRegistry[$tableName];
        } else {
            $this->mdb2Object->loadModule('Reverse', null, true);
            $rawtableInfo = $this->mdb2Object->tableInfo($this->tableName, MDB2_TABLEINFO_ORDER);
            if (PEAR::isError($rawtableInfo)) {
                throw new MDB2_TableBrowser_DBException($rawtableInfo);
            }
            $tableInfo    = array();
            $tableColumns = array();
            for ($count=0;$count <$rawtableInfo['num_fields']; $count++) {
                $columnName                    = $rawtableInfo[$count]['name'];
                $uniformColumnName             = $this->formatIdentifier($columnName);
                $tableInfo[$uniformColumnName] = $rawtableInfo[$count];
                $tableColumns[]                = $columnName;
            }

            $insertColArray = array();
            for ($i=0; $i<count($tableColumns); $i++) {
                $insertColArray[] = '?';
            }
            $insertSQL = 'INSERT INTO ' . $this->tableName . ' VALUES ' . '(' . join(',', $insertColArray) . ')';
            
            self::$tableRegistry[$tableName]['tableInfo'] = $tableInfo;
            self::$tableRegistry[$tableName]['columns']   = $tableColumns;
            self::$tableRegistry[$tableName]['insertSQL'] = $insertSQL;
            
            $this->currentTable = self::$tableRegistry[$tableName];
            return $tableInfo;
        }
    }
    /**
     * Returns all the columns of the table, in the order that they are
     * defined in the table
     *
     * @return array the column names
     */
    public function getColumns()
    {
        return $this->currentTable['columns'];
    }
    /**
     * Gets a properly formated Insert sql statment for the table.
     *
     * @return string
     */
    public function getInsertSQL()
    {
        return $this->currentTable['insertSQL'];
    }
    /**
     * Checks to see if a column exists in the table definition
     *
     * @param string $columnName The column name
     * 
     * @return boolean
     */
    public function isValidColumn($columnName)
    {
        //TODO: Add check to see if db is case insensitive for table/column names
        $tableName  = $this->formatIdentifier($this->tableName);
        $columnName = $this->formatIdentifier($columnName);
        if (array_key_exists($columnName, self::$tableRegistry[$tableName]['tableInfo'])) {
            return true;
        }
        print "Failed to find $columnName\n\n";
        return false;
    }
    /**
     * Checks to see if a row is valid given the table definition
     * 
     * @param array $rowData The row data
     * 
     * @return unknown
     */
    public function isValidRow($rowData)
    {
        foreach ($rowData as $colName => $value) {
            if (!$this->isValidData($colName, $value)) {
                return false;
            }
        }
        return true;
    }
    /**
     * Checks to see if a given column value is valid given the table definition
     *
     * @param string $colName     The column name
     * @param string $columnValue The column value
     * 
     * @return boolean
     */
    public function isValidData($colName, $columnValue)
    {
        //TODO: Implement data type chacking
        return true;
    }
    /**
     * Checks to see if an operator is valid
     *
     * @param string $op The operator eg: '=', '>='
     * 
     * @return boolean
     */
    public function isValidOperator($op)
    {
        //TODO: Implement operator checking
        return true;
    }
    /**
     * Escapes/Quotes a column name (uses mdb2's quoteIdentifier)
     *
     * @param string $columnName The column name
     * 
     * @return string
     */
    public function quoteIdentifier($columnName)
    {
        return $this->mdb2Object->quoteIdentifier($columnName);
    }
    /**
     * Escapes/Quotes a column value (uses mdb2's quote)
     *
     * @param string $data The column value
     * 
     * @return string
     */
    public function quote($data)
    {
        return $this->mdb2Object->quote($data);
    }
}
?>