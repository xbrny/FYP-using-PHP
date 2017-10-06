<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
$survey_id = $_POST["survey_id"];
$department_id = $_POST["department_id"];
$votes_json = getVotesByDepartmentJSON($survey_id, $department_id);
echo $votes_json;
?>
