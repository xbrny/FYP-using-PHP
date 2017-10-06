<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
$survey_id = $_POST["survey_id"];
$votes_json = getVotesJSON($survey_id);
echo $votes_json;
?>
