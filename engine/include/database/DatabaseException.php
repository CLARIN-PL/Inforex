<?php
/**
 * Created by PhpStorm.
 * User: czuk
 * Date: 14.12.18
 * Time: 15:54
 */

class DatabaseException extends Exception
{

    var $details = null;

    public function __construct($message = "", $details = null)
    {
        parent::__construct($message);
        $this->details = $details;
    }

    function getDetails()
    {
        return $this->details;
    }

}
