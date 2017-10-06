<?php
// --------------------subjects----------------------
function getSubjects() {
  global $db;
  $sql = "SELECT * FROM subjects";
  $subjects_result = $db->query($sql) or die("Select subjects failed: " . mysqli_error($db));
  return $subjects_result;
}
function getSubject($id) {
  global $db;
  $sql = "SELECT * FROM subjects WHERE id=$id";
  $subject_result = $db->query($sql) or die("Select subject failed: " . mysqli_error($db));
  return $subject_result;
}
function getSubjectsByMajor($major_id) {
  global $db;
  $sql = "SELECT s.id, s.code, s.name FROM subjects s INNER JOIN subjects_majors sm ON s.id=sm.subject_id WHERE sm.major_id=$major_id";
  $subjects_result = $db->query($sql) or die("Select subjects failed: " . mysqli_error($db));
  return $subjects_result;
}
function getSubjectsByDepartment($department_id) {
  global $db;
  $sql = "SELECT * FROM subjects WHERE department_id=$department_id";
  $subjects_result = $db->query($sql) or die("Select subjects failed: " . mysqli_error($db));
  return $subjects_result;
}
function deleteSubject($id) {
  global $db;
  $sql = "DELETE FROM subjects WHERE id=$id";
  $db->query($sql) or die("Delete subject failed: " . mysqli_error($db));
  $sql = "DELETE FROM comments WHERE subject_id=$id";
  $db->query($sql) or die("Delete comments failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully delete subject";
}
function updateSubject($id, $code, $name, $department_id, $person_in_charge) {
  global $db;
  $sql = "UPDATE subjects SET code='$code', name='$name', department_id=$department_id, user_id=$person_in_charge WHERE id=$id";
  $db->query($sql) or die("Update subject failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully update subject";
}
function updateSubjectForLec($id, $description, $prerequisites, $assessment, $reference) {
  global $db;
  $sql = "UPDATE subjects SET description='$description', prerequisites='$prerequisites', assessment='$assessment', reference='$reference' WHERE id=$id";
  $db->query($sql) or die("Update subject for lecturer failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully update subject";
}
function createSubject($code, $name, $department_id, $user_id, $description="") {
  global $db;
  $sql = "INSERT INTO subjects(code, name, description, department_id, user_id) VALUES('$code', '$name', '$description', '$department_id', '$user_id')";
  $db->query($sql) or die("Insert subject failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully create subject";
}
?>
