<?php

class DbUser{
	
	static function addCorpusRoles($user_id, $corpus_roles = array()){
		
		foreach($corpus_roles as $corpus_id => $roles){
			foreach($roles as $role){
				DbUser::addSingleCorpusRole($user_id, $corpus_id, $role);
			}
		}
		
	}
	
	static function addSingleCorpusRole($user_id, $corpus_id, $role){
		global $db;
		$sql = "INSERT INTO users_corpus_roles(user_id, corpus_id, role) values (?,?,?);";
		$db->execute($sql, array($user_id, $corpus_id, $role));
		$error = $db->errorInfo();
		if(isset($error[0]))
			throw new Exception("Error: (". $error[1] . ") -> ".$error[2]);
	}
	
	/**
	 * Zwraca tablicę z danymi użytkownika z tabeli users.
	 */
	static function get($user_id){
		global $db;
		return $db->fetch("SELECT * FROM users WHERE user_id = ?", array($user_id));
	}

    static function getByLogin($login){
        global $db;
        return $db->fetch("SELECT * FROM users WHERE login = ?", array($login));
    }

    static function getByAuthIdentity($provider, $subject){
        global $db;
        return $db->fetch(
            "SELECT * FROM users WHERE auth_provider = ? AND auth_subject = ?",
            array($provider, $subject)
        );
    }

	static function getByClarinLogin($clarinLogin){
	    global $db;
        return $db->fetch("SELECT * FROM users WHERE clarin_login LIKE ?", array($clarinLogin));
    }

    static function updateClarinUser($id, $clarin_login){
        global $db;
        $sql = "UPDATE users SET `clarin_login` = ? WHERE user_id = ?";
        $db->execute($sql, array($clarin_login, $id));

        $error = $db->errorInfo();
        if(isset($error[0]))
            throw new Exception("Error: (". $error[1] . ") -> ".$error[2]);
    }

    static function updateAuthIdentity($id, $provider, array $claims){
        global $db;
        $sql = "UPDATE users
                SET auth_provider = ?,
                    auth_subject = ?,
                    auth_username = ?,
                    auth_email = ?,
                    auth_email_verified = ?,
                    auth_linked_at = IF(auth_linked_at IS NULL, NOW(), auth_linked_at),
                    last_login_at = NOW()
                WHERE user_id = ?";
        $db->execute($sql, array(
            $provider,
            $claims['subject'],
            $claims['username'],
            $claims['email'],
            $claims['email_verified'] ? 1 : 0,
            $id
        ));

        $error = $db->errorInfo();
        if(isset($error[0]))
            throw new Exception("Error: (". $error[1] . ") -> ".$error[2]);
    }

    static function unlinkAuthIdentity($id){
        global $db;
        $sql = "UPDATE users
                SET auth_provider = NULL,
                    auth_subject = NULL,
                    auth_username = NULL,
                    auth_email = NULL,
                    auth_email_verified = 0,
                    auth_linked_at = NULL
                WHERE user_id = ?";
        $db->execute($sql, array($id));

        $error = $db->errorInfo();
        if(isset($error[0]))
            throw new Exception("Error: (". $error[1] . ") -> ".$error[2]);
    }

    static function updateLastLoginAt($id){
        global $db;
        $db->execute("UPDATE users SET last_login_at = NOW() WHERE user_id = ?", array($id));
    }

    static function verifyLegacyPassword($login, $password){
        global $db;
        $user = $db->fetch(
            "SELECT * FROM users WHERE login = ? AND password = MD5(?)",
            array($login, $password)
        );

        return $user ? $user : null;
    }

    static function createNewUser($login, $screename, $email, $password='None', $clarin_login=null, $authProvider=null, $authClaims=null){
        global $db;
        $sql = "INSERT INTO users (
                    login, screename, email, password, clarin_login,
                    auth_provider, auth_subject, auth_username, auth_email,
                    auth_email_verified, auth_linked_at, last_login_at
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        $db->execute($sql,array(
            $login,
            $screename,
            $email,
            $password,
            $clarin_login,
            $authProvider,
            $authClaims ? $authClaims['subject'] : null,
            $authClaims ? $authClaims['username'] : null,
            $authClaims ? $authClaims['email'] : null,
            $authClaims && $authClaims['email_verified'] ? 1 : 0,
            $authProvider ? date('Y-m-d H:i:s') : null,
            $authProvider ? date('Y-m-d H:i:s') : null
        ));

        $error = $db->errorInfo();
        if(isset($error[0]))
            throw new Exception("Error: (". $error[1] . ") -> ".$error[2]);
    }

    static function getAnonymousActivitiesByYear($reverseOrder = false){
        global $db;

        if($reverseOrder){
            $order = "DESC";
        } else{
            $order = "ASC";
        }

        $sql = "SELECT YEAR(a.datetime) AS year, COUNT(*) AS number_of_activities, COUNT(DISTINCT(a.ip_id)) AS number_of_users FROM activities a
                WHERE a.user_id IS NULL
                GROUP BY YEAR(a.datetime)";
        $sql .= " ORDER BY YEAR(a.datetime) " . $order;

        $activities_years = $db->fetch_rows($sql);

        return $activities_years;
    }

    static function getAnonymousActivitiesByYearMonth($year = null){
        global $db;

        $params = array();
        $where = "a.user_id IS NULL";

        if ($year !== null) {
            $year = intval($year);
            $where .= " AND a.datetime >= ? AND a.datetime < ?";
            $params[] = sprintf("%04d-01-01 00:00:00", $year);
            $params[] = sprintf("%04d-01-01 00:00:00", $year + 1);
        }

        $sql = "SELECT YEAR(a.datetime) AS year, MONTH(a.datetime) as month, COUNT(*) AS number_of_activities, COUNT(DISTINCT(a.ip_id)) AS number_of_users FROM activities a
                WHERE $where
                GROUP BY YEAR(a.datetime), MONTH(a.datetime)
                ORDER BY YEAR(a.datetime) DESC, MONTH(a.datetime) DESC";

        $activities_years_months = $db->fetch_rows($sql, $params);

        return $activities_years_months;
    }

    static function getAnonymousActivitiesByYearMonthChart($year){
        global $db;

        $year = intval($year);
        $startDate = sprintf("%04d-01-01 00:00:00", $year);
        $endDate = sprintf("%04d-01-01 00:00:00", $year + 1);

        $sql = "SELECT YEAR(a.datetime) AS year, DATE_FORMAT(a.datetime, '%b') as month, COUNT(*) AS number_of_activities, COUNT(DISTINCT(a.ip_id)) AS number_of_users FROM activities a
                WHERE a.user_id IS NULL
                  AND a.datetime >= ?
                  AND a.datetime < ?
                GROUP BY YEAR(a.datetime), MONTH(a.datetime)
                ORDER BY YEAR(a.datetime) DESC, MONTH(a.datetime) ASC";

        $activities_years_months = $db->fetch_rows($sql, array($startDate, $endDate));

        return $activities_years_months;
    }

}

?>
