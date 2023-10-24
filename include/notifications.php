<?php

if (is_array($_SESSION[PREFIX . '_action'])) {
    foreach ($_SESSION[PREFIX . '_action'] as $action) {


        /* !Defaults */
        if ($action == 'added') {
            $alert_content = "Added";
            $alert_class = "alert-success";
        }

        if ($action == 'deleted') {
            $alert_content = "Deleted";
            $alert_class = "alert-danger";
        }

        if ($action == 'updated') {
            $alert_content = "Updated";
            $alert_class = "alert-info";
        }

        if ($action == 'imported') {
            $alert_content = "Importing";
            $alert_class = "alert-info";
        }

        if ($action == 'duplicated') {
            $alert_content = "Duplicated";
            $alert_class = "alert-info";
        }

        /* !USERS */
        if ($action == 'loginas') {
            $alert_content = "Logged-in As User";
            $alert_class = "alert-success";
        }


        if ($alert_content) { ?>

            <div class="alert <?php echo $alert_class; ?> alert-dismissible" role="alert">
                <strong><?php echo $alert_content; ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php }
    }//END LOOP
}
$_SESSION[PREFIX . '_action'] = '';
