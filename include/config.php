<?php
//SET UP PHP SESSION

$server_path = dirname(__DIR__) . '';

//PREFIX IS UNIQUE IDENTIFER FOR EACH APP, CREATES UNIQUE SESSION NAME
require_once '/apache2/assets/composer/vendor/autoload.php';
// Load up the .env configuration file
$dotenv = new Dotenv\Dotenv($server_path);
$dotenv->load();

//ini_set('session.save_handler', 'redis');
//ini_set('session.save_path', "tcp://redis:6379");

define('PREFIX', 'boiler');
session_name(PREFIX);

//SETS SESSSION LIFETIME
//ini_set("session.gc_maxlifetime", "3600");

//MAKE THE COOKIE SECURE
//ini_set("session.cookie_secure", "ON");

//CHOOSE WHERE THE COOKIE IS SAVED - SHOULD BE UNDER THE WEB ROOT
//ini_set("session.save_path", "/apache2/uploads/sessions/" . PREFIX);

//THIS IS JUST TO REDUCE PHP ERRORS
date_default_timezone_set("America/Chicago");

//START THE SESSION
session_start();

$app_name = "App Boilerplate";

//CREATE VARIABLES FOR ANY EXTERNAL FILES THAT MAY NEED TO BE INCLUDED ON MULTIPLE PAGES
//SO IF THE FILE NEEDS TO BE UPDATED, IT ONLY HAS TO BE UPDATED IN 1 PLACE
$fontawesome_global = '<link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';

$tinymce_include = '<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=uv5euh0xetyc1zo7v8ph2kfx9xwjwbbmfxtcvund8ujkufdz"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    menubar: false,
    height: 300,
    plugins: [
        "advlist autolink lists link",
        "visualblocks code fullscreen",
        "insertdatetime table paste wordcount"
    ],
    toolbar: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code",
    browser_spellcheck: true
});
</script>';


//INCLUDE ANY MYSQLI OR ORACLE CLASS HERE:
require_once('mysqli_class.php');
$mysqli = new tank();