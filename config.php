<?php
# called by functions
function get_dbc()
{
    $hostname = "localhost";
    $database = "person_group_import";
    $username = "root";
    $password = "";

    $dbc = mysqli_connect($hostname, $username, $password, $database) or trigger_error(mysql_error(), E_USER_ERROR);
    return $dbc;
}

redirectBack()
{
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;
}

/*
Error reporting.
 */
ini_set("error_reporting", "true");
error_reporting(E_ALL | E_STRCT);
