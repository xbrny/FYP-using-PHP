<?php
// --------------------majors----------------------
function getMajors() {
  global $db;
  $sql = "SELECT * FROM majors";
  $majors_result = $db->query($sql) or die("Select majors failed: " . mysqli_error($db));
  return $majors_result;
}
function getMajor($id) {
  global $db;
  $sql = "SELECT * FROM majors WHERE id=$id";
  $major_result = $db->query($sql) or die("Select major failed: " . mysqli_error($db));
  return $major_result;
}
function getMajorsByDepartment($department_id) {
  global $db;
  $sql = "SELECT * FROM majors WHERE id=$department_id";
  $major_result = $db->query($sql) or die("Select major by department failed: " . mysqli_error($db));
  return $major_result;
}
function deleteMajor($id) {
  global $db;
  $sql = "DELETE FROM majors WHERE id=$id";
  $db->query($sql) or die("Delete major failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully delete major";
}
function updateMajor($id, $name) {
  global $db;
  $sql = "UPDATE majors SET name='$name' WHERE id=$id";
  $db->query($sql) or die("Update major failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully update major";
}
function createMajor($name, $department_id) {
  global $db;
  $sql = "INSERT INTO majors(name, department_id) VALUES('$name', '$department_id')";
  $db->query($sql) or die("Insert major failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully create major";
}
?>
