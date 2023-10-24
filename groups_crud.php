<?php

include "include/config.php";

$_SESSION[PREFIX . "_ppage"] = $_SERVER['REQUEST_URI'];
if ($_SESSION[PREFIX . '_username'] == "") {
    header("Location: login.php");
    exit;
}
if ($_SESSION[PREFIX . '_security'] < 15) {
    header("location:index.php?action=5");
    exit;
}

$page_name = "Groups";


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($_POST['crud_form_id']) {
        //UPDATE
        $majorsminors->groups_update($_POST['crud_form_id'], $_POST['name'], $_POST['group_type']);
        $majorsminors->actions_insert(
            $_SESSION[PREFIX . '_username'],
            "Updated Group: " . $_POST['name'] . " (" . $_POST['crud_form_id'] . ")"
        );

        $academics = $majorsminors->academics_groups_list('', $_POST['crud_form_id']);
        $academics_arr = array_column($academics, 'academic_id');
        //REMOVE NEWLY UNSELECTED
        $diff = array_diff($academics_arr, $_POST['programs']);
        foreach ($diff as $remove) {
            $majorsminors->academics_groups_delete($remove, $_POST['crud_form_id']);
        }

        //AND NEWLY SELECTED
        //THIS RELIES ON HAVING A UNIQUE INDEX ON THE TABLE THAT PREVENTS DUPLICATES FROM BEING ADDED
        foreach ($_POST['programs'] as $program) {
            $majorsminors->academics_groups_insert($program, $_POST['crud_form_id']);
        }


        $_SESSION[PREFIX . '_action'][] = 'updated';
        header("location: groups_crud.php");
        exit;
    } else {
        //INSERT
        $majorsminors->groups_insert($_POST['name'], $_POST['group_type']);
        $majorsminors->actions_insert($_SESSION[PREFIX . '_username'], "Added Group: " . $_POST['name']);

        //THIS RELIES ON HAVING A UNIQUE INDEX ON THE TABLE THAT PREVENTS DUPLICATES FROM BEING ADDED
        foreach ($_POST['programs'] as $program) {
            $majorsminors->academics_groups_insert($program, $_POST['crud_form_id']);
        }

        $_SESSION[PREFIX . '_action'][] = 'added';
        header("location: groups_crud.php");
        exit;
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title><?php
        echo $app_name; ?> - <?php
        echo $page_name; ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>

    <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <?php
    require_once "include/layout_head.php"; ?>

    <!-- END HEAD -->
</head>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-sidebar-fixed">
<div class="page-wrapper">
    <!-- BEGIN HEADER -->
    <?php
    require_once "include/layout_header.php"; ?>
    <!-- END HEADER -->
    <!-- BEGIN HEADER & CONTENT DIVIDER -->
    <div class="clearfix"></div>
    <!-- END HEADER & CONTENT DIVIDER -->
    <!-- BEGIN CONTAINER -->
    <div class="page-container">
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar-wrapper">
            <!-- BEGIN SIDEBAR -->
            <?php
            $current_menu = "supporting";
            require_once "include/layout_left_menu.php"; ?>
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
                        <li><a href="groups_crud.php"><?php
                                echo $page_name; ?></a><i class="fa fa-object-group"></i></li>
                    </ul>
                    <div class="page-toolbar">

                    </div>
                </div>
                <!-- END PAGE BAR -->
                <!-- BEGIN PAGE TITLE-->
                <h1 class="page-title"><?php
                    echo $page_name; ?></h1>
                <!-- END PAGE TITLE-->
                <!-- END PAGE HEADER-->
                <div class="page-content-body">
                    <?php
                    include "include/notifications.php"; ?>


                    <div class="row">
                        <div class="col-md-6">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->

                            <table class="table table-striped table-bordered table-hover order-column" id="datatable"
                                   data-order='[[ 0, "desc" ]]' data-paginate='false' data-state-save="true">
                                <thead>
                                <tr>
                                    <th> Group</th>
                                    <th> Type</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php

                                $results = $majorsminors->groups_list();
                                $rcount = count($results);
                                for ($x = 0; $x < $rcount; $x++) {
                                    ?>
                                    <tr>
                                        <td><a class="crud_form_list_item" data-id="<?php
                                            echo $results[$x]['group_id']; ?>"><?php
                                                echo $results[$x]['group_name']; ?></a></td>
                                        <td><?php
                                            echo $results[$x]['group_type_name']; ?></td>

                                    </tr>

                                    <?php
                                } ?>

                                </tbody>
                            </table>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                        <!-- ADD/EDIT FORM -->
                        <div class="col-md-6">
                            <div class="portlet box blue ">
                                <div class="portlet-title">
                                    <div class="caption" id="crud_form_title">
                                        <i class="fa fa-plus"></i>Add <?php
                                        echo $page_name; ?></div>
                                </div>
                                <div class="portlet-body form">
                                    <!-- BEGIN FORM-->
                                    <form action="" method="POST" enctype="multipart/form-data"
                                          class="form-horizontal form-bordered form-row-stripped">
                                        <input type="hidden" id="crud_form_id" name="crud_form_id">
                                        <div class="form-body">

                                            <div class="form-group">
                                                <label class="control-label col-md-2">Name:</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="name" id="name" value=""
                                                           class="form-control" required autofocus>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-md-2">Type:</label>
                                                <div class="col-md-10">
                                                    <select name="group_type" id="group_type" class="form-control">
                                                        <?php
                                                        $results = $majorsminors->group_types_list();
                                                        $rcount = count($results);
                                                        for ($x = 0; $x < $rcount; $x++) { ?>
                                                            <option value="<?php
                                                            echo $results[$x]['group_type_id']; ?>"> <?php
                                                                echo $results[$x]['group_type_name']; ?></option>
                                                        <?php
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <label class="control-label col-md-2">Programs:</label>
                                                <div class="col-md-10">
                                                    <select multiple name="programs[]" id="programs"
                                                            class="form-control">
                                                        <?php
                                                        $results = $majorsminors->academics_list();
                                                        $rcount = sizeof($results);
                                                        for ($x = 0; $x < $rcount; $x++) { ?>
                                                            <option value="<?php
                                                            echo $results[$x]['academic_id']; ?>"> <?php
                                                                echo $results[$x]['academic_name'] . " (" . $results[$x]['degree_level_name'] . ")"; ?></option>
                                                        <?php
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-3 col-md-9">
                                                    <button type="submit" value="delete" class="btn red"
                                                            id="crud_form_button_delete" style="display: none;">
                                                        Deactivate <?php
                                                        echo $page_name; ?></button>
                                                        <button type="submit" value="submit" class="btn blue"
                                                                id="crud_form_button_submit">Add <?php
                                                            echo $page_name; ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- END FORM-->
                                </div>
                            </div>

                        </div>


                    </div><!-- END ROW -->
                </div>


            </div>
            <!-- END CONTENT BODY -->
        </div>
        <!-- END CONTENT -->

    </div>
    <!-- END CONTAINER -->
    <!-- BEGIN FOOTER -->
    <?php
    require_once "include/layout_footer.php"; ?>

    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
            type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../assets/pages/scripts/table-datatables-managed.min.js" type="text/javascript"></script>
    <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <script>
        $(document).ready(function () {
            $('#datatable').DataTable();

            $("#group_type").select2();
            $("#programs").select2();

            $(".crud_form_list_item").click(function () {
                crud_form_load_info($(this).data("id"));
            });


        });

        function crud_form_load_info(id) {
            $.ajax({
                type: "POST",
                url: "include/ajax.php",
                data: {action: "groups_info", id: id},
                success: function (data) {
                    console.log(data);
                    //$("#crud_form_button_delete").show();
                    $("#crud_form_title").html("<i class=\"fa fa-pencil\"></i>Edit <?php echo $page_name;?>")
                    $("#crud_form_button_submit").html("Update <?php echo $page_name;?>")

                    $("#crud_form_id").val(data.group_id);
                    $("#name").val(data.group_name);
                    $("#group_type").val(data.group_type_id).trigger("change");
                    $("#programs").val(data.programs).trigger("change");
                }
            });


        }


    </script>

</body>

</html>