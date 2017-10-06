<?php
// --------------------surveys----------------------
function getSurveys() {
  global $db;
  $sql = "SELECT * FROM surveys ORDER BY id DESC";
  $surveys_result = $db->query($sql) or die("Select surveys failed: " . mysqli_error($db));
  return $surveys_result;
}
function getSurvey($id) {
  global $db;
  $sql = "SELECT * FROM surveys WHERE id=$id";
  $survey_result = $db->query($sql) or die("Select survey failed: " . mysqli_error($db));
  return $survey_result;
}
function getOngoingSurvey() {
  global $db;
  date_default_timezone_set("Asia/Kuala_Lumpur");
  $current_datetime = date("Y-m-d H:i:s");
  $sql = "SELECT * FROM surveys WHERE close_datetime >= '$current_datetime' && open_datetime < '$current_datetime' ORDER BY id DESC LIMIT 1";
  $survey_result = $db->query($sql) or die("Select ongoing survey failed: " . mysqli_error($db));
  return $survey_result;
}
function getPastSurveys() {
  global $db;
  date_default_timezone_set("Asia/Kuala_Lumpur");
  $current_datetime = date("Y-m-d H:i:s");
  $sql = "SELECT * FROM surveys WHERE close_datetime < '$current_datetime' ORDER BY open_datetime desc";
  $survey_result = $db->query($sql) or die("Select ongoing survey failed: " . mysqli_error($db));
  return $survey_result;
}
function getLatestSurveys() {
  global $db;
  $sql = "SELECT * FROM surveys ORDER BY open_datetime DESC LIMIT 3";
  $survey_result = $db->query($sql) or die("Select ongoing survey failed: " . mysqli_error($db));
  return $survey_result;
}
function closeSurvey($id){
  global $db;
  date_default_timezone_set("Asia/Kuala_Lumpur");
  $current_datetime = date("Y-m-d");
  $sql = "UPDATE surveys SET close_datetime='$current_datetime' WHERE id=$id";
  $db->query($sql) or die("Close survey failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully close the survey";
}
function updateSurvey($id, $session, $open_datetime, $close_datetime, $department) {
  global $db;
  $sql = "UPDATE surveys SET session='$session', open_datetime='$open_datetime', close_datetime='$close_datetime', department='$department' WHERE id=$id";
  $db->query($sql) or die("Update survey failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully update survey";
}
function createSurvey($session, $open_datetime, $close_datetime, $department) {
  global $db;
  $sql = "INSERT INTO surveys(session, open_datetime, close_datetime, department) VALUES('$session', '$open_datetime', '$close_datetime', '$department')";
  $db->query($sql) or die("Insert survey failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully create survey";
}
function deleteSurvey($id) {
  global $db;
  $sql = "DELETE FROM surveys WHERE id=$id";
  $db->query($sql) or die("Delete survey failed: " . mysqli_error($db));
  deleteVotesBySurvey($id);
  $_SESSION["success_flash"] = "Successfully delete survey and all votes data";
}
