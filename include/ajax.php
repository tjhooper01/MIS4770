<?php
include "config.php";

require_once "/apache2/assets/upload.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $action = $_POST['action'];
    switch ($action) {
        
        case "colleges_info":
            $info = $majorsminors->colleges_info($_POST['id']);
            
            header("Content-type: application/json");
            echo json_encode($info);
            
            break;
                        
        case "fileupload":
            
            if ($_POST['type'] == "full" && $_FILES['file']['name'] && $_POST['academic_id']) {
                $full = global_upload(basename($_FILES['file']['name']), $_FILES['file']['tmp_name'], "majors-minors/", 0);
                $majorsminors->academics_update_images($_POST['academic_id'], $full[0], '');
            }
            if ($_POST['type'] == "thumb" && $_FILES['file']['name'] && $_POST['academic_id']) {
                $thumb = global_upload(basename($_FILES['file']['name']), $_FILES['file']['tmp_name'], "majors-minors/", 0);
                $majorsminors->academics_update_images($_POST['academic_id'], '', $thumb[0]);
            }
            
            
        
        
            break;
            
    }//END SWITCH
}//END POST
