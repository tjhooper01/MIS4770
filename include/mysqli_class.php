<?php

/*********************************************************************
 * /*## Portal class extends mysqli */
class tank extends mysqli
{
    public function __construct()
    {

        //OR SUPPLY OTHER CONNECTION INFO
        $DBHost = getenv('DATABASE_HOST');
        $DBUser = getenv('DATABASE_USER');
        $DBPass = getenv('DATABASE_PASSWORD');

        //SELECT THE DB
        $databaseName = getenv('DATABASE_NAME');

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        @parent::__construct($DBHost, $DBUser, $DBPass, $databaseName);
        // check if connect errno is set

        //IF THE CONNECTION DOES NOT WORK - REDIRECT TO OUR "DB DOWN" PAGE, BUT PASS THE URL TO THE APPLICATION
        if (mysqli_connect_error()) {
            trigger_error(mysqli_connect_error(), E_USER_WARNING);
            //header("Location: https://www.eiu.edu/web/db_down.php?url=https://www.eiu.edu/apps/metronic/");
            exit;
        }
    }

    /*** QUERY ******************************************************************
     * /*## CreateReadUpdateDelete QUERY FUNCTION */
    public function run_query($query, $type, $params = null)
    {
        if ($stmt = parent::prepare($query)) {
            //IF PARAMS ARE NOT EMPTY, BIND
            if ($params) {
                $ref = new ReflectionClass('mysqli_stmt');
                $method = $ref->getMethod("bind_param");
                if (!$method->invokeArgs($stmt, $params)) {
                    list($callee, $caller) = debug_backtrace(false);
                    $calling_function = "error on bind variables <b>Function:</b> " . $caller['function'] . " <b>Line:</b> " . $callee['line'];
                    trigger_error($this->error . $calling_function, E_USER_WARNING);
                }
            }
            if (!$stmt->execute()) {
                list($callee, $caller) = debug_backtrace(false);
                $calling_function = " <b>Function:</b> " . $caller['function'] . " <b>Line:</b> " . $callee['line'];
                trigger_error($this->error . $calling_function, E_USER_WARNING);
            }
            if (in_array($type, array('info', 'list'))) {
                //FOR SELECT INFO AND LIST TYPES GET META DATA
                $meta = $stmt->result_metadata();
                //BUILD RETURN ARRAY
                while ($field = $meta->fetch_field()) {
                    $parameters[] = &$row[$field->name];
                }

                call_user_func_array(array($stmt, 'bind_result'), $parameters);
            } elseif ($type == 'keyless_list') {
                $stmt->bind_result($value);
            }
            switch ($type) {
                case 'list':
                    //FOR INFO (SINGLE ARRAY)
                    $results = array();
                    while ($stmt->fetch()) {
                        $x = array();
                        foreach ($row as $key => $val) {
                            $x[$key] = $val;
                        }
                        $results[] = $x;
                    }
                    break;
                case 'info':
                    //FOR LIST (MULTI ARRAY)
                    $results = array();
                    $stmt->fetch();
                    $x = array();
                    foreach ($row as $key => $val) {
                        $results[$key] = $val;
                    }
                    break;
                case 'insert':
                    $results = $this->insert_id;
                    break;
                case 'delete':
                case 'update':
                    break;
                case 'keyless_list':
                    //FOR INFO (SINGLE ARRAY)
                    $results = array();
                    while ($stmt->fetch()) {
                        $results[] = $value;
                    }
                    break;
            }
            //RETURN DATA AND CLOSE
            $stmt->close();
            return $results;
        }//END PREPARE
        else {
            list($callee, $caller) = debug_backtrace(false);
            $calling_function = " <b>Function:</b> " . $caller['function'] . " <b>Line:</b> " . $callee['line'];
            trigger_error($this->error . $calling_function, E_USER_WARNING);
        }
    }

    /*** INFO ******************************************************************
     * /*## INFO */
    public function _info($id)
    {
        $query = "
		SELECT 
			*	
		FROM 
			test_table
		WHERE
			id = ?
		";
        //MUST PASS BY REFERENCE (&) infront of variables
        $params = array('i', &$id);
        return $this->run_query($query, 'info', $params);
    }

    /*** LIST ******************************************************************
     * /*## LIST */
    public function _list()
    {
        $query = "
		SELECT 
			*	
		FROM 
			test_table
		";
        //MUST PASS BY REFERENCE (&) infront of variables
        //$params = array('i',$id);
        return $this->run_query($query, 'list', $params);
    }

    /*** UPDATE ******************************************************************
     * /*## UPDATE */
    public function _update($param1, $param2, $param3, $id)
    {
        $query = "
		UPDATE test_table SET 
			test1 = ?,
			test2 = ?,
			test3 = ?	
		WHERE
			id = ?
		";
        //MUST PASS BY REFERENCE (&) infront of variables
        $params = array('issi', &$param1, &$param2, &$param3, &$id);
        return $this->run_query($query, 'update', $params);
    }

    /*** INSERT ******************************************************************
     * /*## INSERT */
    public function _insert($param1, $param2, $param3)
    {
        $query = "
		INSERT INTO test_table
			(
			test1,
			test2,
			test3
			)	
		VALUES
			(?,?,?)
		";
        //MUST PASS BY REFERENCE (&) infront of variables
        $params = array('iss', &$param1, &$param2, &$param3);
        return $this->run_query($query, 'insert', $params);
    }

    /*** DELETE ******************************************************************
     * /*## DELETE */
    public function _delete($id)
    {
        $query = "
		DELETE FROM 
			test_table
		WHERE
			id = ?
		";
        $params = array('i', &$id);
        return $this->run_query($query, 'delete', $params);
    }


    /////////////////
    //!USERS
    /////////////////
    /*** USERS LIST ******************************************************************
     * /*## LIST */
    public function users_list()
    {
        $query = "
		SELECT 
			*	
		FROM 
			users,
			user_levels
		WHERE users.user_level_id = user_levels.user_level_id";

        return $this->run_query($query, "list", $params);
    }

    /*** USERS INFO ******************************************************************
     * /*## INFO */
    public function users_info($id)
    {
        $query = "
		SELECT 
			*	
		FROM 
			users,
			user_levels
		WHERE users.user_level_id = user_levels.user_level_id
		AND	net_id = ?";

        $params = array("s", &$id);
        return $this->run_query($query, "info", $params);
    }

    /*** USERS INSERT ******************************************************************
     * /*## INSERT */
    public function users_insert($net_id, $name, $user_level_id)
    {
        $net_id = strtolower($net_id);
        $query = "
		INSERT INTO users
			(
			net_id,
			name,
			user_level_id)	
		VALUES
			(?,?,?)";

        $params = array("ssi", &$net_id, &$name, &$user_level_id);
        return $this->run_query($query, "insert", $params);
    }

    /*** USERS UPDATE ******************************************************************
     * /*## UPDATE */
    public function users_update($net_id, $name, $user_level_id)
    {
        $query = "
		UPDATE users SET 
			name = ?,
			user_level_id = ?
		WHERE
			net_id = ?";

        $params = array("sis", &$name, &$user_level_id, &$net_id);
        return $this->run_query($query, "update", $params);
    }

    /*** USERS DELETE ******************************************************************
     * /*## DELETE */
    public function users_delete($net_id)
    {
        $query = "
		DELETE FROM 
			users
		WHERE
			net_id = ?";
        $params = array("s", &$net_id);
        return $this->run_query($query, "delete", $params);
    }

    /////////////////
    //!USER_LEVELS
    /////////////////
    /*** USER_LEVELS LIST ******************************************************************
     * /*## LIST */
    public function user_levels_list()
    {
        $query = "
		SELECT 
			*	
		FROM 
			user_levels";

        return $this->run_query($query, "list", $params);
    }

    /*** USER_LEVELS INFO ******************************************************************
     * /*## INFO */
    public function user_levels_info($id)
    {
        $query = "
		SELECT 
			*	
		FROM 
			user_levels
		WHERE
			user_level_id = ?";

        $params = array("i", &$id);
        return $this->run_query($query, "info", $params);
    }

    /*** USER_LEVELS INSERT ******************************************************************
     * /*## INSERT */
    public function user_levels_insert($user_level_name)
    {
        $query = "
		INSERT INTO user_levels
			(
			user_level_name)	
		VALUES
			(?)";

        $params = array("s", &$user_level_name);
        return $this->run_query($query, "insert", $params);
    }

    /*** USER_LEVELS UPDATE ******************************************************************
     * /*## UPDATE */
    public function user_levels_update($user_level_id, $user_level_name)
    {
        $query = "
		UPDATE user_levels SET 
			user_level_name = ?
		WHERE
			user_level_id = ?";

        $params = array("si", &$user_level_name, &$user_level_id);
        return $this->run_query($query, "update", $params);
    }

    /*** USER_LEVELS DELETE ******************************************************************
     * /*## DELETE */
    public function user_levels_delete($id)
    {
        $query = "
		DELETE FROM 
			user_levels
		WHERE
			user_level_id = ?";
        $params = array("i", &$id);
        return $this->run_query($query, "delete", $params);
    }


    //////////////
    //!LOGINS
    //////////////
    /*** LOGIN ******************************************************************
     * /*## LOGS USER INTO APP */
    public function login($net_id, $pass)
    {
        $net_id = strtolower($net_id);
        $login_status = 1;
        $security = 1;
        $query = "
			SELECT 
				users.net_id, 
				users.user_level_id,
				users.name
			FROM 
				users
			WHERE net_id = ?";
        $params = array('s', &$net_id);
        $result = $this->run_query($query, 'info', $params);
        if ($result['net_id'] == $net_id) {
            $user_info = $result;

            //CHECK AD PASSWORD
            include_once "/apache2/assets/ldap_bind.php";
            $login_return = ldap_ad_authenticate($net_id, $pass);
            $login_status = $login_return[0];
            if ($login_status == 1) {
                $this->logins_insert($user_info['net_id']);
            }
        } else {
            $login_status = 0;
            $user_info = array();
        }


        return array($login_status, $user_info);
    }

    /*** LOG LOGINS ******************************************************************
     * /*## Logs user logins  */
    public function logins_insert($username)
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        $refer = $_SERVER['HTTP_REFERER'];
        $query = "
			INSERT INTO logins 
				(net_id,
				login_ip,
				login_browser)	
			VALUES
				(?,?,?)";

        $params = array('sss', &$username, &$ip, &$agent);
        return $this->run_query($query, 'insert', $params);
    }

    /*** PRUNE LOGINS ******************************************************************
     * /*## Prunes Logins  */
    public function logins_prune()
    {
        $fdate = date("Y-m-d", strtotime("-180 days"));

        $query = "
			DELETE FROM logins 
				WHERE login_timestamp < ?";

        $params = array('s', &$fdate);
        return $this->run_query($query, 'delete', $params);
    }


    /*** VIEW LOGINS ******************************************************************
     * /*## QUERIES Logins  */
    public function logins_list()
    {
        $query = "
			SELECT 
				*	
			FROM 
				logins
			ORDER BY login_timestamp desc
			LIMIT 1000";

        return $this->run_query($query, 'list', $params);
    }

    ///////////
    //!ACTIONS
    ///////////
    /*** LOG ACTIONS ******************************************************************
     * /*## Logs Action  */
    public function actions_insert($username, $action)
    {
        $page = $_SERVER['REQUEST_URI'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        $refer = $_SERVER['HTTP_REFERER'];
        $query = "
			INSERT INTO actions 
				(net_id,
				action_desc,
				action_page,
				action_ip,
				action_browser,
				action_refer)	
			VALUES
				(?,?,?,?,?,?)";

        $params = array('ssssss', &$username, &$action, &$page, &$ip, &$agent, &$refer);
        return $this->run_query($query, 'insert', $params);
    }


    /*** VIEW ACTIONS ******************************************************************
     * /*## QUERIES Action  */
    public function actions_list()
    {
        $query = "
			SELECT 
				*	
			FROM 
				actions
			ORDER BY action_timestamp desc
			LIMIT 1000";

        return $this->run_query($query, 'list', $params);
    }

    /*** PRUNE ACTIONS ******************************************************************
     * /*## Prunes Action  */
    public function actions_prune()
    {
        $query = "
		DELETE FROM actions
				WHERE action_timestamp < DATE_SUB(NOW(), INTERVAL 36 MONTH)";
        return $this->run_query($query, 'delete', $params);
    }
}//END CLASS
