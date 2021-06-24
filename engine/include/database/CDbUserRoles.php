<?php

class DbUserRoles
{

    /**
     * Return user roles
     * @param $user_id int identifier
     * @return array of role names, i.e. array("admin")
     */
    static function get($user_id)
    {
        global $db;
        $sql = "SELECT role FROM users_roles WHERE user_id=?";
        return $db->fetch_ones($sql, "role", array($user_id));
    }

    /**
     * Set roles for user.
     * @param $user_id User identifier
     * @param $roles Array of roles to set
     */
    static function set($user_id, $roles)
    {
        global $db;
        $db->execute("BEGIN");
        $db->execute("DELETE FROM users_roles WHERE user_id = ?", array($user_id));
        foreach ($roles as $role) {
            $db->execute("INSERT INTO users_roles (user_id, role) VALUES(?, ?)",
                array($user_id, $role));
        }
        $db->execute("COMMIT");
    }

    /**
     * Return all roles
     * @return data of roles
     */
    static function getAllRoles()
    {
        global $db;
        return $db->fetch_rows("SELECT * FROM roles ORDER BY description");
    }

}

?>