<?php
//phpinfo();exit;

$starttime = microtime(true); // Top of page




define('PREFIX', 'boiler');
session_name(PREFIX);

//SETS SESSSION LIFETIME
//ini_set("session.gc_maxlifetime", "3600");

//MAKE THE COOKIE SECURE
//ini_set("session.cookie_secure", "ON");

//CHOOSE WHERE THE COOKIE IS SAVED - SHOULD BE UNDER THE WEB ROOT
//ini_set("session.save_path", "/tmp/");

//THIS IS JUST TO REDUCE PHP ERRORS
date_default_timezone_set("America/Chicago");

//START THE SESSION
session_start();

print_r(session_get_cookie_params(), 1);

$_SESSION['test_data'.rand(1,1000)] = "sauce".rand(1,1000);
echo session_id() . "<br>";

echo "<pre>";print_r($_SESSION);echo "</pre>";

$now = microtime(true); // Bottom of page
echo "Session started " . ($now - $starttime) . " seconds<br>";

$filepath = "/apache2/uploads/test/";

$now = microtime(true); // Bottom of page
echo "File write started " . ($now - $starttime) . " seconds<br>";
//write a basic file
$myfile = fopen($filepath . "newfile.txt", "w") or die("Unable to open file!");
$txt = "John Doe\n";
fwrite($myfile, $txt);
fclose($myfile);
$now = microtime(true); // Bottom of page
echo "Finished writing " . ($now - $starttime) . " seconds<br>";

$now = microtime(true); // Bottom of page
echo "start read" . ($now - $starttime) . " seconds<br>";
//read file
$myfile = fopen($filepath . "newfile.txt", "r") or die("Unable to open file!");
echo fread($myfile, filesize("newfile.txt"));
fclose($myfile);
$now = microtime(true); // Bottom of page
echo "end read" . ($now - $starttime) . " seconds<br>";


phpinfo();

exit;
//GET IP GEOLOCATION

$geolocation = json_decode(
    file_get_contents(
        "http://api.ipstack.com/" . explode(
            ',',
            $_SERVER['HTTP_X_FORWARDED_FOR']
        )[0] . "?access_key=7d0d832bead7013d49f30d7953988919&format=1"
    ),
    true
);

echo "<pre>";
print_r($geolocation);
echo "</pre>";


//GET DATA FROM DATA-API EXAMPLE
$post = ['key' => getenv('DATA_API_KEY'), 'action' => 'student_info', 'enumber' => 'E12570127'];

$ch = curl_init('https://www.eiu.edu/apps/data-api/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

// execute!
$results = json_decode(curl_exec($ch), true);

// close the connection, release resources used
curl_close($ch);
