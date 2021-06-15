<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbSens
{

    static private $__queryMiddleContent = " FROM annotation_types_attributes ata JOIN annotation_types at ON ata.annotation_type_id = at.annotation_type_id";
    static private $__queryOrderPhrase = " ORDER BY ata.annotation_type_id";

    /**
     * Get all annotation_types_attributes with their type names
     *   and return result as an array of assoc arrays.
     * @param $fields - if set custom returned column set
     *         defaults to: " ata.*, at.name AS 'annotation_name' "
     * @param $rowCount - only chunk of max rowCount results
     * @param $startIndex - records in results started from startIndex row
     * @return {Array} Array of rows, each one as associative array
     *              'field_name' => field_value for each field
     */
    static function getSensList($fields = null, $rowCount = null, $startIndex = 0, $searchPhrase = null)
    {

        global $db;
        $sql = " SELECT " .
            ($fields ? $fields : " ata.*, at.name AS 'annotation_name' ") .
            self::$__queryMiddleContent;
        $limits = "";
        if ($rowCount) {
            $limits = strval($rowCount);
        }
        if ($startIndex) {  // offset from 0 ( defaults )
            $limits = strval($startIndex) . ", " . $limits;
        }
        if ($limits) {
            $limits = " LIMIT " . $limits;
        }

        $search = "";
        if ($searchPhrase) {
            $search = " WHERE at.name LIKE '%" . $searchPhrase . "%'";
        }


        $sql .= $search . self::$__queryOrderPhrase . $limits;

        return $db->fetch_rows($sql);

    } // getSensList()

    static function getSensListCount($searchPhrase = null)
    {

        global $db;
        $sql = " SELECT " .
            " COUNT(*) " .
            self::$__queryMiddleContent;

        $search = "";
        if ($searchPhrase) {
            $search = " WHERE at.name LIKE '%" . $searchPhrase . "%'";
        }

        $sql .= $search . self::$__queryOrderPhrase;

        return $db->fetch_one($sql);

    } // getSensListCount()


    static function getSensDataById($sens_id, $fields = null)
    {
        global $db;
        $sql = " SELECT " .
            ($fields ? $fields : " * ") .
            " FROM annotation_types_attributes_enum " .
            " WHERE annotation_type_attribute_id=? ";

        return $db->fetch_rows($sql, array($sens_id));
    }

}

?>
