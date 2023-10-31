<?php
include "include/config.inc";

$_SESSION[PREFIX . "_ppage"] = $_SERVER['REQUEST_URI'];
if ($_SESSION[PREFIX . '_username'] == "") {
    header("Location: login.php");
    exit;
}
if ($_SESSION[PREFIX . '_security'] < 5) {
    header("location:index.php?action=5");
    exit;
}

$page_name = "Student Add";

if ($_SERVER['REQUEST_METHOD'] == "POST") {


    $mysqli->student_insert($_POST['student_fname'], $_POST['student_lname'], $_POST['student_email'], $_POST['student_phone'], $_POST['student_dob']);

    $mysqli->actions_insert("Added Student: " . $_POST['student_fname'] . " " . $_POST['student_lname'], $_SESSION[PREFIX . '_user_id']);


    $_SESSION[PREFIX . '_action'][] = 'added';
    header("location: student_list.php");
    exit;
}//END POST


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $app_name; ?> - <?php echo $page_name; ?></title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png"/>

</head>
<body>
<div class="container-scroller">

    <?php require_once 'partials/_navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
        <?php require_once 'partials/_sidebar.php'; ?>
        <div class="main-panel">
            <div class="content-wrapper">

                <div class="row">
                    <div class="col-md-12 grid-margin">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div class="d-flex align-items-end flex-wrap">
                                <div class="me-md-3 me-xl-5">
                                    <h2><?php echo $page_name; ?></h2>
                                    <p class="mb-md-0">text</p>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Horizontal Form</h4>
                                <p class="card-description">
                                    Horizontal form layout
                                </p>
                                <form class="forms-sample" id="form1" action="" method="post">

                                    <div class="form-group row">
                                        <label for="student_fname" class="col-sm-3 col-form-label">First Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="student_fname"
                                                   name="student_fname"
                                                   placeholder="First Name" required autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="student_lname" class="col-sm-3 col-form-label">Last Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="student_lname"
                                                   name="student_lname"
                                                   placeholder="Last Name" required>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label for="student_email" class="col-sm-3 col-form-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="student_email"
                                                   name="student_email"
                                                   placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="student_phone" class="col-sm-3 col-form-label">Phone</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="student_phone"
                                                   name="student_phone"
                                                   placeholder="Phone">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="student_dob" class="col-sm-3 col-form-label">Date of Birth</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" id="student_dob" name="student_dob"
                                                   placeholder="Date of Birth">
                                        </div>
                                    </div>


                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    <button class="btn btn-light">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
            <!-- content-wrapper ends -->
            <?php require_once 'partials/_footer.php'; ?>
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->

<!-- plugins:js -->
<script src="vendors/base/vendor.bundle.base.js"></script>
<!-- endinject -->
<!-- Plugin js for this page-->
<script src="vendors/chart.js/Chart.min.js"></script>
<script src="vendors/datatables.net/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
<!-- End plugin js for this page-->
<!-- inject:js -->
<script src="js/off-canvas.js"></script>
<script src="js/hoverable-collapse.js"></script>
<script src="js/template.js"></script>
<!-- endinject -->
<!-- Custom js for this page-->


<!-- End custom js for this page-->

<script src="js/jquery.cookie.js" type="text/javascript"></script>
</body>

</html>

