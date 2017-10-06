<?php
// --------------------surveys----------------------
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/subjects_operation.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/surveys_operation.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/votes_operation.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/users_operation.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/departments_operation.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/majors_operation.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/comments_operation.php";

function sanitize($dirty) {
  return htmlentities($dirty, ENT_QUOTES, "UTF-8");
}

function pretty_date($date) {
  return date("d M Y H:i A", strtotime($date));
}

function resetPassword($id, $email) {
  $password = mt_rand(100000,999999);
  $password_hash = password_hash($password, PASSWORD_DEFAULT);
  $email_content = "Send to: $email. Here is your new temporary password to login into ESIS: $password\n";
  $user_email_file = fopen(BASE_URL."/email_file/user_email.txt", "a") or die("Unable to open file");
  fwrite($user_email_file, $email_content);
  fclose($user_email_file);
  updatePassword($id, $password_hash);
  $_SESSION["success_flash"] = "Successfully reset user password, a temporary password has been sent to user email";
}

function display_errors($errors) {
  $str = '<div class="errors-div"><div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><ul>';
  foreach($errors as $error) {
    $str .= "<li><strong>$error</strong></li>";
  }
  $str .= '</ul></div></div>';
  echo $str;
}

function is_logged_in() {
  if(isset($_SESSION["user_id"])) {
    return true;
  }
  return false;
}

function is_permission($permission) {
  global $user_data;
  if($_SESSION["permission"] == $permission) {
    return true;
  }
  return false;
}

function check_home_redirect() {
  $home_page = "/".APP_NAME."/index";
  if(is_logged_in()) {
    if(is_permission("student")) {
      $home_page = "/".APP_NAME."/student/index";
      error_permission_redirect("$home_page");

    } elseif(is_permission("lecturer")) {
      $home_page = "/".APP_NAME."/lecturer/index";
      error_permission_redirect("$home_page");

    } elseif(is_permission("hod")) {
      $home_page = "/".APP_NAME."/hod/index";
      error_permission_redirect("$home_page");

    } elseif(is_permission("admin")) {
      $home_page = "/".APP_NAME."/admin/index";
      error_permission_redirect("$home_page");
    }
  }
}

function error_login_redirect($page = "/".APP_NAME."/index") {
  $_SESSION["error_flash"] = "You must be logged in first to access that page";
  header("Location: $page");
}

function error_permission_redirect($page = "/".APP_NAME."/index") {
  $_SESSION["error_flash"] = "You don't have right permission to access that page";
  header("Location: $page");
}

function login($user_id) {
  $_SESSION["user_id"] = $user_id;
  $user_data_result = getUser($user_id);
  $user_data = mysqli_fetch_assoc($user_data_result);
  $user_department_result = getDepartmentByUser($user_id);
  $user_department = mysqli_fetch_assoc($user_department_result);
  if($user_data["permission"] == "hod") {
    $role = "head of department";
  } else {
    $role = $user_data["permission"];
  }
  // $_SESSION["permission"] = $user_data["permission"];
  $_SESSION["department_id"] = $user_department["id"];
  $_SESSION["department_name"] = $user_department["name"];
  $_SESSION["permission"] = $user_data["permission"];
  $_SESSION["success_flash"] = "You are now logged in as $role";
  $app_name = APP_NAME;
  if($user_data["permission"] == "student") {
    header("Location: /$app_name/student/index");
  } elseif($user_data["permission"] == "lecturer") {
    header("Location: /$app_name/lecturer/index");
  } elseif($user_data["permission"] == "hod") {
    header("Location: /$app_name/hod/index");
  } elseif($user_data["permission"] == "admin") {
    header("Location: /$app_name/admin/index");
  }
}

function logout($page = 'index') {
  unset($_SESSION["user_id"]);
  unset($_SESSION["department_id"]);
  unset($_SESSION["permission"]);
  unset($_SESSION["department_name"]);
  session_unset();
  $_SESSION["success_flash"] = "You have logged out from your account";
  header("Location: $page");
}
?>
