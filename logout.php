<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
if(is_logged_in()) {
  logout();
} else {
  header("Location: index");
}
?>
