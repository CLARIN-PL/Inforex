<?php
/**
 * This file contains a class that implements the OrderByClauseGenerator
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
class MDB2_TableBrowser_OrderByManager implements MDB2_TableBrowser_InterfaceOrderByClauseGenerator
{
    
    //Stores a reference to an object that implements 
    protected $validator = null;

    //Stores the orderBy list
    protected $orderByList = array();
    //Stores the orderBy list order, i.e the order the columns were added to the list
    protected $orderByColumnOrder = array();
    
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
     * Order results. Multiple columns can be added
     *
     * @param string $colName   The column name
     * @param string $direction The direction either ASC or DESC, (optional)
     * 
     * @return void
     */
    public function setOrderBy($colName, $direction = null)
    {
        if (!$this->validator->isValidColumn($colName)) {
            throw new MDB2_TableBrowser_ParameterException("Invalid column '$colName'", array($colName, $direction));
        }
        $strColName = $this->validator->formatIdentifier($colName);
        if (!array_key_exists($strColName, $this->orderByList)) {
            $this->orderByList[$strColName] = $direction;
            $this->orderByColumnOrder[]     = $colName;
        }
    }
    /**
     * Clears all sorting columns
     *
     * @return void
     */
    public function resetOrderBy()
    {
        $this->orderByColumnOrder = array();
        $this->orderByList        = array();
    }
    /**
     * Generates the select clause of an sql statement
     *
     * @return string the select clause
     */
    public function generateSQL()
    {
        $orderByClause = array();
        foreach ($this->orderByColumnOrder as $column) {
            $strColName = $this->validator->formatIdentifier($column);
            $direction  = $this->orderByList[$strColName];
            if (!is_null($direction)) {
                $orderByClause[] = $this->validator->quoteIdentifier($column) . ' ' . $direction;
            } else {
                $orderByClause[] = $this->validator->quoteIdentifier($column);
            }
        }
        return join(', ', $orderByClause);
    }
}
?>