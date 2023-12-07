<?php

/*********************************************************************
 * /*## Portal class extends mysqli */
class mysqli_class extends mysqli
{
    public function __construct()
    {

        //OR SUPPLY OTHER CONNECTION INFO
        $DBHost = '43.231.234.179';
        $DBUser = 'group2';
        $DBPass = '2UTyvKnuAKA3mYCxWzY6jdyR2s';

        //SELECT THE DB
        $databaseName = 'group2';

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        @parent::__construct($DBHost, $DBUser, $DBPass, $databaseName);
        // check if connect errno is set

        //IF THE CONNECTION DOES NOT WORK - REDIRECT TO OUR "DB DOWN" PAGE, BUT PASS THE URL TO THE APPLICATION
        if (mysqli_connect_error()) {
            trigger_error(mysqli_connect_error(), E_USER_WARNING);
            echo mysqli_connect_error();
            exit;
        }
    }


    /*** LIST ******************************************************************
     * /*## List all data */
    public function student_list()
    {
        $results = array();
        $query = "
			SELECT 
				*	
			FROM 
				students		
			ORDER BY student_lname";

        if ($stmt = parent::prepare($query)) {
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $parameters);

            while ($stmt->fetch()) {
                $x = array();
                foreach ($row as $key => $val) {
                    $x[$key] = $val;
                }
                $results[] = $x;
            }
            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }
        return $results;
    }

    /*** INFO ******************************************************************
     * /*## Gets info for a row */
    public function student_info($id)
    {

        $results = array();
        $query = "
			SELECT 
				*	
			FROM 
				students
			WHERE
				student_id = ?";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $parameters);

            $stmt->fetch();
            $x = array();
            foreach ($row as $key => $val) {
                $results[$key] = $val;
            }
            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

        return $results;

    }

    //ADD new students
    public function student_insert($student_fname, $student_lname, $student_email, $student_phone, $student_dob)
    {
        $query = "
			INSERT INTO students 
				(student_fname,
				student_lname,
				student_email,
				student_phone,
				student_dob)	
			VALUES
				(?,?,?,?,?)";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("sssss", $student_fname, $student_lname, $student_email, $student_phone, $student_dob);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $last_id = $this->insert_id;

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

        return $last_id;

    }

    //EDIT students

    /*** EDIT  ******************************************************************
     * /*## Updates row */
    public function student_edit($student_id, $student_fname, $student_lname, $student_email, $student_phone, $student_dob)
    {

        $query = "
			UPDATE students SET 
				student_fname = ?,
				student_lname = ?,
				student_phone = ?,
				student_email = ?,
				student_dob = ?	
			WHERE
				student_id=?";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("sssssi", $student_fname, $student_lname, $student_phone, $student_email, $student_dob, $student_id);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

    }

    /*** REMOVE  ******************************************************************
     * /*## removes row */
    public function student_delete($id)
    {

        $query = "
			DELETE FROM 
				table_name 
			WHERE
				id = ?";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

    }

    /**** LOGIN ******************************************************************
     * /*## Checks login credentials */
    public function login($email, $password)
    {

        $query = "SELECT * FROM users WHERE email = ?";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("s", $email);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $parameters);

            $stmt->fetch();
            $x = array();
            foreach ($row as $key => $val) {
                $x[$key] = $val;
            }
            $stmt->close();

            if ($x['email'] == $email && password_verify($password, $x['user_password'])) {
                $this->logins_insert($x['user_id']);
                return array(1, $x);
            } else {
                return array(0, $x);
            }

        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }
    }

    /*** LOG LOGINS ******************************************************************
     * /*## Logs user logins  */
    public
    function logins_insert($user_id)
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        $refer = $_SERVER['HTTP_REFERER'];
        $query = "
			INSERT INTO logins 
				(user_id,
				login_ip,
				login_browser)	
			VALUES
				(?,?,?)";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("iss", $user_id, $ip, $agent);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $last_id = $this->insert_id;

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }
        return $last_id;
    }

    //ADD actions logging
    public
    function actions_insert($action, $user_id)
    {
        $page = $_SERVER['REQUEST_URI'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        $refer = $_SERVER['HTTP_REFERER'];
        $query = "
			INSERT INTO actions 
				(user_id,
				action_desc,
				action_page,
				action_ip,
				action_browser,
				action_refer)	
			VALUES
				(?,?,?,?,?,?)";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("ssssss", $user_id,
                $action, $page, $ip, $agent, $refer);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $last_id = $this->insert_id;

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

        return $last_id;
    }

//////////////
//USERS
/////////////

    /*** INFO ******************************************************************
     * /*## Gets info for a row */
    public
    function user_info($user_id)
    {

        $results = array();
        $query = "
			SELECT
				*
			FROM
				users
			WHERE
				user_id = ?";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("i", $user_id);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $parameters);

            $stmt->fetch();
            $x = array();
            foreach ($row as $key => $val) {
                $results[$key] = $val;
            }
            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }
        return $results;
    }

    /*** USER LIST ******************************************************************
     * /*## List all data */
    public
    function user_list()
    {
        $results = array();
        $query = "
			SELECT
				*
			FROM
				users,
				user_levels
			WHERE users.user_level_id = user_levels.user_level_id
			ORDER BY user_id";
        //echo $query;
        if ($stmt = parent::prepare($query)) {
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $parameters);

            while ($stmt->fetch()) {
                $x = array();
                foreach ($row as $key => $val) {
                    $x[$key] = $val;
                }
                $results[] = $x;
            }
            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }
        return $results;
    }

    /*** USER ADD  ******************************************************************
     * /*## adds row  data */
    public
    function user_insert($email, $name, $password, $level)
    {
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "
			INSERT INTO users
				(email,
				 user_name,
				 user_password,
				 user_level_id)
			VALUES
				(?,?,?,?)";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("sssi", $email, $name, $pass_hash, $level);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
                $last_id = 0;
            }
            $last_id = $this->insert_id;

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
            $last_id = 0;
        }

        return $last_id;

    }

    /*** USER EDIT  ******************************************************************
     * /*## Updates row */
    public
    function user_edit($user_id, $email, $name, $password, $level)
    {
        if ($password) {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $pass_hash = $this->user_info($user_id)['user_password'];
        }
        $query = "
			UPDATE users SET
				email = ?,
				user_name = ?,
				user_password = ?,
				user_level_id = ?
			WHERE
				user_id = ?";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("sssii", $email, $name, $pass_hash, $level, $user_id);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

    }

    /*** USER REMOVE  ******************************************************************
     * /*## removes row */
    public
    function user_remove($user_id)
    {

        $query = "
			DELETE FROM
				users
			WHERE
				user_id = ?";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("i", $user_id);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }
    }

    /*** USER LEVEL LIST ******************************************************************
     * /*## List all data */
    public
    function user_level_list()
    {
        $results = array();
        $query = "
			SELECT
				*
			FROM
				user_levels
			ORDER BY user_level_id";

        if ($stmt = parent::prepare($query)) {
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $parameters);

            while ($stmt->fetch()) {
                $x = array();
                foreach ($row as $key => $val) {
                    $x[$key] = $val;
                }
                $results[] = $x;
            }
            $stmt->close();

        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

        return $results;
    }

    public function sock_list()
    {
        $results = array();
        $query = "
			SELECT 
				*	
			FROM 
				Socks	
			ORDER BY socktype";

        if ($stmt = parent::prepare($query)) {
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $parameters);

            while ($stmt->fetch()) {
                $x = array();
                foreach ($row as $key => $val) {
                    $x[$key] = $val;
                }
                $results[] = $x;
            }
            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }
        return $results;
    }

    public function sock_insert($sockname, $socktype, $sockprice)
    {
        $query = "
			INSERT INTO Socks
				(sockname,
				socktype,
				sockprice)	
			VALUES
				(?,?,?)";
        if ($stmt = parent::prepare($query)) {
            //GIVE SOCKPRICE A VALUE IF IT IS OUT OF RANGE OR IS NOT A DOUBLE
            if (($sockprice > (double)PHP_INT_MAX) || ($sockprice < (double)PHP_INT_MIN) || (!is_double($sockprice))) {
                $sockprice = 99.99;
            }
            $stmt->bind_param("ssd", $sockname, $socktype, $sockprice);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $last_id = $this->insert_id;

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

        return $last_id;

    }

    public function sock_info($id)
    {

        $results = array();
        $query = "
			SELECT 
				*	
			FROM 
				Socks
			WHERE
				id = ?";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $parameters);

            $stmt->fetch();
            $x = array();
            foreach ($row as $key => $val) {
                $results[$key] = $val;
            }
            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

        return $results;

    }

    public function sock_edit($id, $sockname, $socktype, $sockprice)
    {

        $query = "
			UPDATE Socks SET 
				sockname = ?,
				socktype = ?,
				sockprice = ?
			WHERE
				id=?";
        if ($stmt = parent::prepare($query)) {
            if ($sockprice > (double)PHP_INT_MAX || $sockprice < (double)PHP_INT_MIN || !is_double($sockprice)) {
                $sockprice = 99.99;
            }
            $stmt->bind_param("ssdi", $sockname, $socktype, $sockprice, $id);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

    }

    /** SHOWS START HERE */
    public function show_list()
    {
        $results = array();
        $query = "
			SELECT 
				*	
			FROM 
				shows
			ORDER BY votes DESC";

        if ($stmt = parent::prepare($query)) {
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $parameters);

            while ($stmt->fetch()) {
                $x = array();
                foreach ($row as $key => $val) {
                    $x[$key] = $val;
                }
                $results[] = $x;
            }
            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }
        return $results;
    }

    public function show_insert($showname, $year, $runtime, $votes, $genres, $description)
    {
        $query = "
			INSERT INTO shows
				(show_name,
				year,
				runtime,
				votes,
				genres,
				description)	
			VALUES
				(?,?,?,?,?,?)";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("siiiss", $showname, $year, $runtime, $votes, $genres, $description);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $last_id = $this->insert_id;

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

        return $last_id;

    }

    public function show_genre_insert($show_id, $genre_id)
    {
        $query = "
			INSERT INTO shows_genres
				(show_id,
				 genre_id)
			VALUES
				(?,?)";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("ii", $show_id, $genre_id);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $last_id = $this->insert_id;

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

        return $last_id;

    }

    public function show_info($id)
    {

        $results = array();
        $query = "
			SELECT 
				*	
			FROM 
				shows
			WHERE
				id = ?";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $parameters);

            $stmt->fetch();
            $x = array();
            foreach ($row as $key => $val) {
                $results[$key] = $val;
            }
            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

        return $results;

    }

    public function show_edit($id, $showname, $year, $runtime, $votes, $genres, $description)
    {

        $query = "
			UPDATE shows SET 
				show_name = ?,
				year = ?,
				runtime = ?,
				votes = ?,
				genres = ?,
				description = ?
			WHERE
				id=?";
        if ($stmt = parent::prepare($query)) {
            $stmt->bind_param("siiissi", $showname, $year, $runtime, $votes, $genres, $description, $id);
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }

            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }

    }

    public function review_add($id, $review_value, $review_content, $review_date, $show_id, $user_id)
    {

    }

    public function show_list_home()
    {
        $results = array();
        $query = "
			SELECT 
				*	
			FROM 
				shows
			ORDER BY votes DESC
			LIMIT 10";

        if ($stmt = parent::prepare($query)) {
            if (!$stmt->execute()) {
                trigger_error($this->error, E_USER_WARNING);
            }
            $meta = $stmt->result_metadata();
            while ($field = $meta->fetch_field()) {
                $parameters[] = &$row[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $parameters);

            while ($stmt->fetch()) {
                $x = array();
                foreach ($row as $key => $val) {
                    $x[$key] = $val;
                }
                $results[] = $x;
            }
            $stmt->close();
        }//END PREPARE
        else {
            trigger_error($this->error, E_USER_WARNING);
        }
        return $results;
    }
}//END CLASS
