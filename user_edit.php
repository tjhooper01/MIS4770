<?php
include "include/config.php";

$_SESSION[PREFIX."_ppage"] = $_SERVER['REQUEST_URI'];
if ($_SESSION[PREFIX.'_username'] == "") {
    header("Location: login.php");
    exit;
}
if ($_SESSION[PREFIX.'_security'] < 15) {
    header("location:index.php?action=5");
    exit;
}

//ONLY ADMINS CAN EDIT OTHER USERS
if ($_SESSION[PREFIX.'_username'] != $_GET['id']) {
    if ($_SESSION[PREFIX.'_security'] < 15) {
        header("location:index.php?action=5");
        exit;
    }
}

$in_user = $_GET['id'];
if ($in_user != '') {
    $userinfo = $mysqli->users_info($in_user);
} else {
        header("location:user_list.php");
        exit;
    }

$page_name ="Edit User";


$action = $_GET['action'];
if ($action == 'remove' && $_SESSION[PREFIX.'_security'] > 10) {
    $user = $_GET['id'];
    $mysqli->users_delete($user);
    $mysqli->actions_insert($_SESSION[PREFIX.'_username'], "Deleted User: ".$user);
    $_SESSION[PREFIX.'_action'][] = 'deleted';
    header("location: user_list.php");
    exit;
}


if ($action == 'loginas') {
    $user = $_GET['id'];
    $mysqli->actions_insert($_SESSION[PREFIX.'_username'], "Logged in as: ".$user);
    
    $_SESSION[PREFIX.'_username'] 	= $userinfo['net_id'];
    $_SESSION[PREFIX.'_security'] 	= $userinfo['user_level_id'];
    $_SESSION[PREFIX.'_fullname']	= $userinfo['user_fullname'];
    
    $_SESSION[PREFIX.'_action'][] = 'loginas';
    header("location: user_list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $mysqli->users_update($_POST['netid'], $_POST['fullname'], $_POST['level']);
    $mysqli->actions_insert($_SESSION[PREFIX.'_username'], "Updated User: ".$_POST['netid']);
    $_SESSION[PREFIX.'_action'][] = 'updated';
    header("location: user_edit.php?id=".$_POST['netid']);
    exit;
}//END POST



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
                                <li><a href="user_list.php">Users</a><i class="fa fa-user"></i></li>
                                <li><a href="user_add.php"><?php echo $page_name;?></a><i class="fa fa-user"></i></li>
                            </ul>
                            <div class="page-toolbar">
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"> Actions<i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><a onclick="confirm_delete();">Delete User</a></li>
										<?php if ($_SESSION[PREFIX.'_security'] > 10) {	?>
											<li><a href="?action=loginas&id=<?php echo $in_user;?>">Login-As</a></li>
										<?php } ?>
                                    </ul>
                                </div>
                            </div>
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
                                
                                <div class="portlet box blue ">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-user"></i><?php echo $page_name;?></div>
                                    </div>
                                    <div class="portlet-body form">
                                        <!-- BEGIN FORM-->
                                        <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal form-bordered form-row-stripped">
	                                        <input type="hidden" name="netid" value="<?php echo $in_user;?>">
                                            <div class="form-body">
	                                            
                                                <div class="form-group">
                                                    <label class="control-label col-md-2">EIU NetID <small>(without @eiu.edu)</small>:</label>
                                                    <div class="col-md-10">
                                                        <input type="text" name="netid" id="netid" autofocus readonly onchange="netid_check('netid');" class="form-control" value="<?php echo $userinfo['net_id'];?>">
										<span class="help-block" id="netid_msg"></span>
                                                    </div>
                                                </div>
                                               <div class="form-group">
													<label class="col-md-2 control-label">Fullname:</label>
													<div class="col-md-10">
														<input type="text" name="fullname" id="fullname" class="form-control" required value="<?php echo $userinfo['name'];?>">
													</div>
												</div>
                                                <div class="form-group">
													<label class="col-md-2 control-label" >User Level:</label>
													<div class="col-md-10">
														<select name="level" id="level"  class="form-control">
														<?php
                                                            $user_level_list = $mysqli->user_levels_list();
                                                            
                                                            for ($x = 0; $x < count($user_level_list); $x++) { ?>
															<option value="<?php echo $user_level_list[$x]['user_level_id'];?>" <?php if ($user_level_list[$x]['user_level_id'] == $userinfo['user_level_id']) {
                                                                echo " selected ";
                                                            }?> ><?php echo $user_level_list[$x]['user_level_name'];?></option>			
														<?php } ?>
														
														</select>
													</div>
												</div>
                                                
                                            </div>
                                            <div class="form-actions">
                                                <div class="row">
                                                    <div class="col-md-offset-3 col-md-9">
                                                        <button type="submit" class="btn green">
                                                            <i class="fa fa-check"></i> Submit</button>
                                                        <button type="button" class="btn default">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <!-- END FORM-->
                                    </div>
                                </div>
                                
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
		
		function confirm_delete()
		{
		 var where_to= confirm("Do you really want to delete this user?");
		 if (where_to== true){ window.location="?action=remove&id=<?php echo $_GET['id'];?>";}
		}

		
		function netid_check(field){
			var in_netid = document.getElementById(field);
			var msg_field = document.getElementById(field+"_msg");
			
			$.ajax({
				dataType: "json",
				url: "/_eiu15/include/ajax.php",
				data: {type: 5, netid: in_netid.value}
			})
			  .done(function( data ) {
			  	if(data.status == 1){
				  	//.innerHTML();
				  	$(msg_field).html("<span class='validation_correct'><i class='fa fa-check-circle'></i> "+data.fullname+"</span>");
				  	$('#fullname').val(data.fname + " "+data.lname);
				  	//$('#phone').val(data.phone);
				  	//$('#email').val(data.email);
				  	//$('#enum').val(data.enumber);
			  	}else{
				  	$(msg_field).html("<span class='validation_incorrect'><i class='fa fa-exclamation-circle'></i> Invalid Netid try again</span>");
			  	}
			  });
			
			
		}
		
		
		</script>

    </body>

</html>