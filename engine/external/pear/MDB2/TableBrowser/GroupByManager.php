<?php
/**
 * This file contains a class that implements the GroupByClauseGenerator
 * interface
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
class MDB2_TableBrowser_GroupByManager implements MDB2_TableBrowser_InterfaceGroupByClauseGenerator
{
    
    //Stores a reference to an object that implements 
    protected $validator = null;

    //Stores the groupBy list
    protected $groupByList = array();
    //Stores the orderBy list order, i.e the order the columns were added to the list
    protected $groupByColumnOrder = array();
    
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
     * Groups rows. Multiple can be groupBys can specified
     *
     * @param string $colName The column name
     * 
     * @return void
     */
    public function setGroupBy($colName)
    {
        if (!$this->validator->isValidColumn($colName)) {
            throw new MDB2_TableBrowser_ParameterException("Invalid column '$colName'", array($colName, $direction));
        }
        $strColName = $this->validator->formatIdentifier($colName);
        if (!array_key_exists($strColName, $this->groupByList)) {
            $this->groupByList[$strColName] = $colName;
            $this->groupByColumnOrder[]     = $colName;
        }
    }
    /**
     * Clears all sorting columns
     *
     * @return void
     */
    public function resetGroupBy()
    {
        $this->groupByColumnOrder = array();
        $this->groupByList        = array();
    }
    /**
     * Generates the select clause of an sql statement
     *
     * @return string the select clause
     */
    public function generateSQL()
    {
        $groupByColumns = array();
        foreach ($this->groupByColumnOrder as $column) {
            $groupByColumns[] = $this->validator->quoteIdentifier($column);
        }
        $grouBYClause = join(', ', $groupByColumns);
        return $grouBYClause;
    }
}
?>