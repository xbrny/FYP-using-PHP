<?php
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "esis_db");
define("BASE_URL", $_SERVER["DOCUMENT_ROOT"]."/esis");
define("APP_NAME", "esis");

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("Database connection error " . mysqli_error_connect());

session_start();
require_once("helpers.php");
?>
