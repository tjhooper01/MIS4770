<?php

class eiumisc_oci
{
    private function db_connect()
    {
        // connect using .env vars
        if ($conn = oci_pconnect(getenv('OCI_USER'), getenv('OCI_PASSWORD'), getenv('OCI_CONN_STR'))) {
            return $conn;
        } else {
            $e = oci_error();
            trigger_error("Can't connect to PROD ODS: " . $e['message'], E_USER_WARNING);
        }
    }


    private function oci_query($query, $type, $params = null)
    {
        // init connection
        $conn = $this->db_connect();

        // parse query
        if ($stid = oci_parse($conn, $query)) {
            // bind params
            if ($params) {
                foreach ($params as $name => $val) {
                    oci_bind_by_name($stid, $name, $params[$name]);
                }
            }
            // fetch results
            if (oci_execute($stid)) {

                // build return by type
                switch ($type) {

                    // LIST TYPE (multi row)
                    case 'list':
                        $x = 0;
                        // build array
                        while ($row = oci_fetch_assoc($stid)) {
                            $results[$x] = $row;
                            $x++;
                        }
                        break;

                    // INFO TYPE (single row)
                    case 'info':
                        $results = oci_fetch_assoc($stid);
                        break;

                    case 'update':
                    case 'insert':
                    case 'delete':
                        $results = oci_num_rows($stid);
                        break;
                }

                // free statement and return
                oci_free_statement($stid);
                oci_close($conn);
                return $results;
            } else {
                $error = print_r(oci_error($stid), 1);
                trigger_error($error, E_USER_WARNING);
            }
        } else {
            $error = print_r(oci_error($conn), 1);
            trigger_error($error, E_USER_WARNING);
        }
    }

    /* QUERY METHODS */

    // DOC: list courses for given faculty netid and term
    public function faculty_course_list($fac_netid, $cur_sem)
    {
        $query = "
		SELECT 
			* 
		FROM 
			EIUMISC.EIU_FAC_ENROLL 
		WHERE 
			netid = :fac_netid
		AND 
			ACADEMIC_PERIOD = :cur_sem
		ORDER BY SUBJECT, COURSE_NUMBER, SECTION
		";
        $params = array(':fac_netid' => &$fac_netid, ':cur_sem' => &$cur_sem);
        return $this->oci_query($query, 'list', $params);
    }

    // DOC: grab info for given crn and term
    public function faculty_course_info($crn, $term)
    {
        $query = "
		SELECT
			a.*, b.TITLE
		FROM 
			eiumisc.EIU_FAC_ENROLL a
		JOIN
			eiumisc.EIU_COURSE b
		ON
			(a.SUBJECT = b.SUBJECT AND a.COURSE_NUMBER = b.COURSE_NUMBER AND a.ACADEMIC_PERIOD = b.ACADEMIC_PERIOD)
		WHERE
			a.ACADEMIC_PERIOD = :term
		AND
			a.COURSE_REF_NUM = :crn
		";
        $params = array(':term' => &$term, ':crn' => &$crn);
        return $this->oci_query($query, 'info', $params);
    }

    // DOC: updates row, marks as processed
    public function awarded_degree_update($pidm, $term)
    {

        $query = "
		UPDATE        
		    EIUMISC.EIU_AWARDED_DEGREEz
        SET AWARDED_DEGREE_PROCESS_DATE = systimestamp
        WHERE AWARDED_DEGREE_PIDM = :pidm
        AND AWARDED_DEGREE_TERM = :term";
        //echo $query;
        $params = array(':term' => &$term, ':pidm' => &$pidm);
        return $this->oci_query($query, 'info', $params);
    }
}//END CLASS
