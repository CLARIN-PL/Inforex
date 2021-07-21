<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

// dla dostępności MDB2::MDB2_PORTABILITY_NONE przy inicjowaniu $db tylko
require_once(__DIR__."/../../../engine/external/pear/MDB2.php");

/**
 * Database gateway.
 */
class Database{

    var $mdb2 = null;
    var $log = false;

    /**
     * @param dsn {array}
     * @param log {boolean} -- print logs (default: false)
     * @param log_output {String} -- where to print logs: fb (use fb function), print (use print),
     */
    function __construct($dsn, $log=false, $log_output="chrome_php", $encoding="utf8mb4"){
        $options = array('portability' => MDB2_PORTABILITY_NONE);
        $options['debug']=2;
        $options['result_buffering']=false;
        // to eliminate some problems with prepare statements
        $options['emulate_prepared']=true;
        $this->mdb2 =& MDB2::connect($dsn, $options);
        if (PEAR::isError($this->mdb2)) {
            throw new DatabaseException($this->mdb2->getMessage());
        }
        $this->mdb2->loadModule('Extended');
        $this->set_encoding($encoding);
        $this->mdb2->query("SET SESSION query_cache_type = ON");
        $this->log = $log;
        $this->log_output = $log_output;
    }

    /**
     * Log out and disconnect from the database.
     */
    function disconnect(){
        $this->mdb2->disconnect();
    }

    /**
     * reset encoding to comunicate with database
     */
    public function set_encoding($encoding) {
        $this->mdb2->query("SET CHARACTER SET '$encoding'");
        $this->mdb2->query("SET NAMES '$encoding'");
    } // set_encoding()

    /**
     * Log message using Database internal logger.
     */
    function log_message($message){
        if ( $this->log ){
            if ($this->log_output == "print"){
                print '<pre>\n'.$message.'</pre>\n';
            }
            elseif ($this->log_output == "chrome_php"){
                ChromePhp::log($message);
            }
        }
    }

    /**
     * Log SQL statement with backtrace of its execution.
     * @param $sql {String} SQL query.
     * @param $args {Array} Query arguments
     */
    function log_sql($sql, $args){
        if ( $this->log ){
            $backtrace = array();
            foreach (debug_backtrace() as $d){
                $backtrace[] = sprintf("File %s, line %d, %s%s%s(...)",
                    $d['file'], $d['line'], $d['class'], $d['type'],
                    $d['function']);
            }

            if ($this->log_output == "print"){
                $msg = "SQL LOG\n";
                $msg .= $sql . "\n";
                $msg .= implode("\n", $backtrace);
                $msg .= print_r($args, true);
                print '<pre>\n'.$msg.'</pre>\n';
            }
            elseif ($this->log_output == "chrome_php"){
                ChromePhp::log($sql);
                ChromePhp::log($args);
                ChromePhp::log($backtrace);
            }
            elseif ($this->log_output == "fb"){
                FB::info($sql, "SQL LOG");
                fb($args, "Args");
                fb($backtrace, "Backtrace");
            }
            else {
                throw new DatabaseException("Unknown log mode ".$this->log_output.". Expected one of the following: print, chrome_php, fb");
            }
        }
    }

    /**
     * Execute query with optional argument and return result of the execution.
     * @param $sql {String} SQL query.
     * @param $args {Array} Query argumnets.
     */
    function execute($sql, $args=null){
        $time_start = microtime(TRUE);
        $sth = null;
        $result = null;
        try{
            $this->log_sql($sql, $args);
            if ($args == null){
                if (PEAR::isError($result = $this->mdb2->query($sql))){
                    print("<pre>{$result->getUserInfo()}</pre>");
                    throw new DatabaseException($result->getMessage());
                }
            }else{
                if (PEAR::isError($sth = $this->mdb2->prepare($sql))){
                    print("<pre>{$sth->getUserInfo()}</pre>");
                    throw new DatabaseException($sth->getMessage());
                }
                $result = $sth->execute($args);
                if (PEAR::isError($result)){
                    throw new DatabaseException($result->getMessage() . "\n" . $result->getUserInfo(), $result);
                }
                if ($this->log){
                    $this->log_message($args, "SQL DATA");
                }
            }
            if ($this->log)
                $this->log_message('Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
        }
        catch(Exception $ex){
            if ( $sth !== null && !PEAR::isError($sth) ){
                $sth->free();
            }
            throw $ex;
        }
        if ( $sth !== null && !PEAR::isError($sth) ){
            $sth->free();
        }
        return $result;
    }

    /**
     * Execute query and return result as an assoc array.
     * @param $sql {String} SQL query.
     * @param $args {Array} Query arguments.
     * @return {Array} Array of arrays (rows)
     */
    function fetch_rows($sql, $args = null){
        return $this->execute($sql, $args)->fetchAll(MDB2_FETCHMODE_ASSOC);
    }

    /**
     * Return one-dimensional array of values for given column for each row
     * returned by the query.
     * @param $sql {String} SQL query.
     * @param $column {String} Column name.
     * @param $args {Array} Query arguments.
     * @return {Array} An array of strings, i.e. array("one", "two", "three")
     */
    function fetch_ones($sql, $column, $args = null){
        $rows = $this->fetch_rows($sql, $args);
        $vals = array();
        foreach ($rows as $row){
            if(array_key_exists($column,$row)) {
                $vals[] = $row[$column];
            } else { // error
                throw new DatabaseException(
                    "Column $column doesn't exists in results of $sql query.",
                    array(  "sql"=>$sql,
                        "column" => $column,
                        "args" => $args
                    )
                );
            }
        }
        return $vals;
    }


    /**
     * Return a one-dimensional array of values representing a single row
     * returned by the query.
     * @param $sql {String} SQL query.
     * @param $args {Array} Query arguments.
     * @return {Array} An assoc array of strings.
     */
    function fetch($sql, $args=null){
        $result = $this->fetch_rows($sql,$args);
        return is_array($result) && (count($result)>0) ? $result[0] : array() ;

    }


    /**
     * Return a single value for the first row.
     */
    function fetch_one($sql, $args=null){
        $r = $this->execute($sql, $args);
        return $r->fetchOne();
    }

    /**
     *
     */
    function fetch_id($table_name){
        return $this->mdb2->getAfterID(0, $table_name);
    }

    /**
     *
     */
    function last_id(){
        return $this->mdb2->lastInsertID();
    }

    /**
     * Update row values from a table for given key.
     * @param table Name of a table.
     * @param values Assoc array with values to update, i.e. array("column"=>"value")
     * @param keys Assoc array with keys, i.e. array("key"=>"value")
     */
    function update($table, $values, $keys){
        $value = "";
        if(is_array($values)){
            foreach ($values as $k=>$v)
                $value[] = "`$k`=?";
        } else {
            throw new DatabaseException("2-nd argument of Database->update() must be an array.",$values);
        }
        if(!is_array($value)) {
            // followed implode() fails....
            throw new DatabaseException("2-nd argument of Database->update() must be non empty array.",$values);
        }
        $key = "";
        if(is_array($keys)){
            foreach ($keys as $k=>$v)
                $key[] = "`$k`=?";
        } else {
            throw new DatabaseException("3-rd argument of Database->update() must be an array.",$keys);
        }
        if(!is_array($key)) {
            // followed implode() fails....
            throw new DatabaseException("3-rd argument of Database->update() must be non empty array.",$keys);
        }
        $sql = "UPDATE $table SET ".implode(", ", $value)." WHERE ".implode(" AND ", $key);
        $args = array_merge(array_values($values), array_values($keys));
        $this->execute($sql, $args);
    }


    /**
     * Inserts a row with values to given table.
     * @param $table Name of a table
     * @param $attributes Assoc table with colument and values, i.e. array("column"=>"value")
     */
    function insert($table, $values){
        $cols = array();
        $vals = array();
        if(is_array($values)){
            foreach ($values as $k=>$v){
                $cols[] = "`$k`";
                $vals[] = "?";
            }
        } else {
            throw new DatabaseException("2-nd argument of Database->insert() must be an array.",$values);
        }
        if((!is_array($cols)) or (!is_array($vals))) {
            // followed implode() fails....
            throw new DatabaseException("2-nd argument of Database->insert() must be non empty array.",$values);
        }
        $sql = "INSERT INTO `$table` (".implode(",", $cols).") VALUES(".implode(",", $vals).")";
        $this->execute($sql, array_values($values));
    }

    /**
     * Inserts multiple rows to a single table.
     * @param $table Name of a table
     * @param $columns Array with column names.
     * @param $values Array of array of column values.
     */
    /*	function insert_bulk($table, $columns, $values){
            $params = array();
            $cols = array();
            $fs = array();
            foreach ($columns as $column){
                $cols[] = "`$column`";
                $fs[] = "?";
            }
            $field = "(".implode(", ", $fs).")";
            $fields = array();
            foreach ($values as $vs){
                foreach ($vs as $v){
                    $params[] = $v;
                }
                $fields[] = $field;
            }
            $sql = "INSERT INTO $table(".implode(",", $cols).") VALUES ".implode(",", $fields);
            $this->execute($sql, $params);
        }	*/
    function insert_bulk($table, $columns, $values){
        $params = array();
        $cols = array();
        $fs = array();
        if(is_array($columns)){ // if not, foreach fails
            foreach ($columns as $column){
                $cols[] = "`$column`";
                $fs[] = "?";
            }
        } else {
            throw new DatabaseException("2-nd argument of Database->insert_bulk() must be an array.",$values);
        }
        if(!is_array($fs)) {
            // followed implode() fails....
            throw new DatabaseException("2-nd argument of Database->insert_bulk() must be non empty array.",$values);
        }
        $field = "(".implode(", ", $fs).")";
        $fields = array();
        try {
            foreach ($values as $vs){
                foreach ($vs as $v){
                    $params[] = $v;
                }
                $fields[] = $field;
            }
        } catch (Exception $e) {
            throw new DatabaseException('Bad parameter $values - should be non empty array of arrays');
        }
        $sql = "INSERT INTO $table(".implode(",", $cols).") VALUES ".implode(",", $fields);
        $this->execute($sql, $params);
    }


    /**
     * Insert or replace row for the keys.
     */
    function replace($table, $values){
        $value = array();
        $params = array();
        try {
            foreach ($values as $k=>$v){
                $value[] = "`$k`=?";
                $params[] = $v;
            }
            $implodedPhrase = implode(", ", $value);
        } catch (Exception $e) {
            throw new DatabaseException('Bad parameter $values - should be non empty array');
        }
        $sql = "REPLACE `$table` SET ".$implodedPhrase;
        $this->execute($sql, $params);
    }


    /*	function select($table, $values){
            $value = array();
            $params = array();
            foreach ($values as $k=>$v){
                $value[] = "`$k`=?";
                $params[] = $v;
            }
            $sql = "SELECT * FROM `$table` WHERE ".implode(" AND ", $value);
            return $this->fetch_rows($sql, $params);
        }*/
    function select($table, $values){
        $value = array();
        $params = array();
        try {
            foreach ($values as $k=>$v){
                $value[] = "`$k`=?";
                $params[] = $v;
            }
            $implodedPhrase = implode(" AND ", $value);
        } catch (Exception $e) {
            throw new DatabaseException('Bad parameter $values - should be non empty array');
        }
        $sql = "SELECT * FROM `$table` WHERE ".$implodedPhrase;
        return $this->fetch_rows($sql, $params);
    }


    /**
     * @param $table
     * @param $keyColumn
     * @param $values
     */
    function get_entry_key($table, $keyColumn, $values){
        $sql = "SELECT $keyColumn FROM $table";
        $params = array();
        $wheres = array();
        try {
            foreach ($values as $k=>$v){
                $wheres[] = "`$k`=?";
                $params[] = $v;
            }
        } catch (Exception $e) {
            throw new DatabaseException('Bad parameter $values - should be non empty array');
        }

        if ( count($wheres)>0 ){
            $sql .= " WHERE " . implode(" AND ", $wheres);
        }
        $keys = $this->fetch_ones($sql, $keyColumn, $params);
        if ( count($keys) == 0 ){
            $this->insert($table, $values);
            return $this->last_id();
        } else {
            return $keys[0];
        }
    }

    /**
     * Execute query and return result as an assoc array.
     * @param $class_name {String} Name of the class
     * @param $sql {String} SQL query.
     * @param $args {Array} Query arguments.
     * @return {Array} Array of instance of $class_name with attributtes
     * 		sets to name and values from selected rows
     */
    /*	public function fetch_class_rows($class_name, $sql, $args = null){
                $rows = $this->fetch_rows($sql, $args);
                $objects = array();
                foreach ($rows as $row){
                        $o = new $class_name();
                        foreach ($row as $k=>$v)
                                $o->$k = $v;
                        $objects[] = $o;
                }
                return $objects;
        } // fetch_class_rows() */
    public function fetch_class_rows($class_name, $sql, $args = null){
        if(!class_exists($class_name)) {
            throw new DatabaseException('First argument of fetch_class_rows method from Database class must be a valid class name');
        }
        $rows = $this->fetch_rows($sql, $args);
        $objects = array();
        foreach ($rows as $row){
            $o = new $class_name();
            foreach ($row as $k=>$v)
                $o->$k = $v;
            $objects[] = $o;
        }
        return $objects;
    } // fetch_class_rows()


    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param   string  text string value that is intended to be converted.
     * @param   string  type to which the value should be converted to
     * @param   bool    quote
     * @param   bool    escape wildcards
     *
     * @return  string  text string that represents the given argument value in
     *       a DBMS specific format.
     */

    public function quote($value, $type = null, $quote = true, $escape_wildcards = false)
    {
        return $this->mdb2->quote($value,$type,$quote,$escape_wildcards);
    }

    // TODO: check strictly and replace by other implemented methods
    public function fetchAll($sql) {
        return $this->mdb2->query($sql)->fetchAll();
    } // fetchAll()

    // TODO: change to something more flexible after checking in tests
    public function errorInfo() {

        return $this->mdb2->errorInfo();

    } // errorInfo()

    public function escape($text) {

        return $this->mdb2->escape($text);
    }
    /**
     * Return associative array of values from two selected columns
     * for each row returned by the query.
     * @param $sql {String} SQL query.
     * @param $key_column_name {String} Column name for key value
     * @param $value_column_name {String} Column name for value value
     * @param $args {Array} Query arguments.
     * @return {Array} An associative array of pairs key=>value
     */
    function fetch_assoc_array($sql, $key_column_name, $value_column_name, $args = null){
        $rows = $this->fetch_rows($sql, $args);
        $result = array();
        foreach ($rows as $row){
            $result[$row[$key_column_name]] = $row[$value_column_name];
        }
        return $result;
    } // fetch_assoc_array()

    /**
     * Execute the specified query, fetch the value from the first column of
     * the first row of the result set and then frees
     * the result set.
     *
     * @param string $query  the SELECT query statement to be executed.
     * @param string $type   optional argument that specifies the expected
     *                       datatype of the result set field, so that an eventual
     *                       conversion may be performed. The default datatype is
     *                       text, meaning that no conversion is performed
     * @param mixed  $colnum the column number (or name) to fetch
     *
     * @return  mixed   MDB2_OK or field value on success, a MDB2 error on failure
     *
     * @access  public
     */

    function queryOne($query)
    {
        $result = $this->mdb2->query($query,null);
        if (!MDB2::isResultCommon($result)) {
            return $result;
        }

        $one = $result->fetchOne(0);
        $result->free();
        return $one;
    }

}

?>