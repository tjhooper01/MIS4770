<?php
include "include/config.php";

$_SESSION[PREFIX."_ppage"] = $_SERVER['REQUEST_URI'];
if ($_SESSION[PREFIX.'_username'] == "") {
    header("Location: login.php");
    exit;
}
if ($_SESSION[PREFIX.'_security'] < 1) {
    header("location:login.php");
    exit;
}

$page_name = "Boilerplate";
if ($tank) {
    echo "Sauce";
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo $app_name;?> - <?php echo $page_name;?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />

			<?php require_once "include/layout_head.php";?>

    <!-- END HEAD -->
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-sidebar-fixed">
        <div class="page-wrapper">
            <!-- BEGIN HEADER -->
            	<?php require_once "include/layout_header.php";?>
            <!-- END HEADER -->
            <!-- BEGIN HEADER & CONTENT DIVIDER -->
            <div class="clearfix"> </div>
            <!-- END HEADER & CONTENT DIVIDER -->
            <!-- BEGIN CONTAINER -->
            <div class="page-container">
                <!-- BEGIN SIDEBAR -->
                <div class="page-sidebar-wrapper">
                    <!-- BEGIN SIDEBAR -->
                    <?php $current_menu = "admin"; require_once "include/layout_left_menu.php";?>
                </div>
                <!-- END SIDEBAR -->
                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">
                        <!-- BEGIN PAGE HEADER-->
                        
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li><a href="index.php">Home</a><i class="fa fa-circle"></i></li>
                            </ul>
                            
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"><?php echo $page_name;?></h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="page-content-body">
                            <?php include "include/notifications.php";?>
                            
                            
                            <div class="row">
                            <div class="col-md-12">
                              
                              <h2>This is your starting place</h2>
                              <p>You can view all samples of code for Metronic here: <a href="https://www.eiu.edu/apps/metronic_sample/">https://www.eiu.edu/apps/metronic_sample/</a></p>
	                              
                              
                              
                            </div>
                            </div>
                        </div>
                            
                            
                            
                            
                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->
               
            </div>
            <!-- END CONTAINER -->
            <!-- BEGIN FOOTER -->
            <?php require_once "include/layout_footer.php";?>
        
        <!-- BEGIN PAGE LEVEL PLUGINS -->

        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->

        <!-- END PAGE LEVEL SCRIPTS -->
		
		<script>
		$(document).ready( function () {
		    $('#datatable').DataTable();
		} );
		</script>

    </body>

</html>