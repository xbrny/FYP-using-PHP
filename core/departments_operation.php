<?php
// --------------------departments----------------------
function getDepartments() {
  global $db;
  $sql = "SELECT * FROM departments";
  $departments_result = $db->query($sql) or die("Select departments failed: " . mysqli_error($db));
  return $departments_result;
}
function getDepartment($id) {
  global $db;
  $sql = "SELECT * FROM departments WHERE id=$id";
  $department_result = $db->query($sql) or die("Select department failed: " . mysqli_error($db));
  return $department_result;
}
function getDepartmentByUser($user_id) {
  global $db;
  $sql = "SELECT d.id, d.name FROM departments d INNER JOIN users u ON d.id=u.department_id WHERE u.id=$user_id";
  $department_result = $db->query($sql) or die("Select department failed: " . mysqli_error($db));
  return $department_result;
}
function deleteDepartment($id) {
  global $db;
  $sql = "DELETE FROM departments WHERE id=$id";
  $db->query($sql) or die("Delete department failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully delete department";
}
function updateDepartment($id, $name) {
  global $db;
  $sql = "UPDATE departments SET name='$name' WHERE id=$id";
  $db->query($sql) or die("Update department failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully update department";
}
function createDepartment($name) {
  global $db;
  $sql = "INSERT INTO departments(name) VALUES('$name')";
  $db->query($sql) or die("Insert department failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully create department";
  return mysqli_insert_id($db);
}
?>
