<?php
// --------------------users----------------------
function getUsers() {
  global $db;
  $sql = "SELECT * FROM users";
  $users_result = $db->query($sql) or die("Select users failed: " . mysqli_error($db));
  return $users_result;
}
function getUser($id) {
  global $db;
  $sql = "SELECT * FROM users WHERE id=$id";
  $user_result = $db->query($sql) or die("Select user failed: " . mysqli_error($db));
  return $user_result;
}
function getUserLogin($login_id) {
  global $db;
  $sql = "SELECT * FROM users WHERE login_id='$login_id'";
  $user_result = $db->query($sql) or die("Select user by login in failed: " . mysqli_error($db));
  return $user_result;
}
function getUserByEmail($email){
  global $db;
  $sql = "SELECT * FROM users WHERE email='$email'";
  $user_result = $db->query($sql) or die("Select user by email failed: " . mysqli_error($db));
  return $user_result;
}
function getUserByPermission($permission){
  global $db;
  $sql = "SELECT * FROM users WHERE permission='$permission'";
  $user_result = $db->query($sql) or die("Select user by permission failed: " . mysqli_error($db));
  return $user_result;
}
function getUserByLoginID($login_id) {
  global $db;
  $sql = "SELECT * FROM users WHERE login_id='$login_id'";
  $user_result = $db->query($sql) or die("Select user failed: " . mysqli_error($db));
  return $user_result;
}
function getUsersByDepartment($department_id) {
  global $db;
  $sql = "SELECT * FROM users WHERE department_id=$department_id";
  $user_result = $db->query($sql) or die("Select user by department failed: " . mysqli_error($db));
  return $user_result;
}
function getHodByDepartment($department_id) {
  global $db;
  $sql = "SELECT * FROM users WHERE department_id=$department_id && permission='hod'";
  $user_result = $db->query($sql) or die("Select user by department failed: " . mysqli_error($db));
  return $user_result;
}
function getStudentsByDepartment($department_id) {
  global $db;
  $sql = "SELECT * FROM users WHERE department_id=$department_id && permission='student'";
  $user_result = $db->query($sql) or die("Select student by department failed: " . mysqli_error($db));
  return $user_result;
}
function getLecturersByDepartment($department_id) {
  global $db;
  $sql = "SELECT * FROM users WHERE department_id=$department_id && (permission='lecturer' OR permission='hod')";
  $user_result = $db->query($sql) or die("Select user failed: " . mysqli_error($db));
  return $user_result;
}
function getTeachingLecturer($subject_id) {
  global $db;
  $sql = "SELECT u.id, u.login_id, u.full_name FROM subjects s INNER JOIN users u ON s.user_id=u.id WHERE s.id=$subject_id";
  $user_result = $db->query($sql) or die("Select teaching lecturer failed: " . mysqli_error($db));
  return $user_result;
}
function getLecturerCount() {
  global $db;
  $sql = "SELECT COUNT(*) AS total FROM users WHERE permission='lecturer'";
  $user_result = $db->query($sql) or die("getLecturerCount failed: " . mysqli_error($db));
  return $user_result;
}
function getStudentCount() {
  global $db;
  $sql = "SELECT COUNT(*) AS total FROM users WHERE permission='student'";
  $user_result = $db->query($sql) or die("getStudentCount failed: " . mysqli_error($db));
  return $user_result;
}
function getLecturersCountByDepartment($department_id) {
  global $db;
  $sql = "SELECT COUNT(*) AS total FROM users WHERE (permission='lecturer' OR  permission='hod') && department_id=$department_id";
  $user_result = $db->query($sql) or die("getLecturerCount failed: " . mysqli_error($db));
  return $user_result;
}
function getStudentsCountByDepartment($department_id) {
  global $db;
  $sql = "SELECT COUNT(*) AS total FROM users WHERE permission='student' && department_id=$department_id";
  $user_result = $db->query($sql) or die("getStudentCount failed: " . mysqli_error($db));
  return $user_result;
}
function updateAnswered($id){
  global $db;
  $sql = "UPDATE users SET survey_answered=1 WHERE id=$id";
  $db->query($sql) or die("Update user answered survey failed: " . mysqli_error($db));
}
function resetAnswered(){
  global $db;
  $sql = "UPDATE users SET survey_answered=0";
  $db->query($sql) or die("Reset users answered survey failed: " . mysqli_error($db));
}
function updatePassword($id, $hashed_password){
  global $db;
  $sql = "UPDATE users SET password='$hashed_password' WHERE id=$id";
  $db->query($sql) or die("Update user password failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully update password";
}
function updateUserNameEmail($id, $name, $email) {
  global $db;
  $sql = "UPDATE users SET full_name='$name', email='$email' WHERE id=$id";
  $db->query($sql) or die("Update user name and email failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully update your profile";
}
function createUser($full_name, $email, $login_id, $password, $permission, $department_id, $major_id) {
  global $db;
  if($department_id == 0 && $major_id == 0) { //admin without major and department
    $sql = "INSERT INTO users(full_name, email, login_id, password, permission)
            VALUES('$full_name', '$email', '$login_id', '$password', '$permission')";
  } elseif($major_id == 0) { //lecturer without major
    $sql = "INSERT INTO users(full_name, email, login_id, password, permission, department_id)
            VALUES('$full_name', '$email', '$login_id', '$password', '$permission', $department_id)";
  } else {
    $sql = "INSERT INTO users(full_name, email, login_id, password, permission, department_id, major_id)
            VALUES('$full_name', '$email', '$login_id', '$password', '$permission', $department_id, $major_id)";
  }
  $db->query($sql) or die("Insert user failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully create user, a temporary password has been sent to user email";
}
function deleteUser($id) {
  global $db;
  $sql = "DELETE FROM users WHERE id=$id";
  $db->query($sql) or die("Delete user failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully delete user";
}
function updateUser($id, $full_name, $email, $login_id, $permission, $department_id=0, $major_id=0) {
  global $db;
  $sql = "UPDATE users SET full_name='$full_name', email='$email', login_id='$login_id',
          permission='$permission', department_id=$department_id, major_id=$major_id WHERE id=$id";
  $db->query($sql) or die("Update user failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully update user";
}

?>
