<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/admin/includes/header.php";
// ------------------------------------------------------------------------------------ //
if(!is_logged_in()) {
  error_login_redirect();
}

$full_name = "";
$email = "";
$password = "";
$o_password = "";
$n_password = "";
$c_password = "";

$user_result = getUser($user_id);
$user = mysqli_fetch_assoc($user_result);
$full_name = $user["full_name"];
$email = $user["email"];
$password = $user["password"];

if(isset($_POST["update_profile"])) {
  $errors = array();
  $full_name = sanitize($_POST["full_name"]);
  $email = sanitize($_POST["email"]);
  $o_email = sanitize($_POST["old_email"]);

  if(empty($full_name) || empty($email)) {
    $errors[] = "Must fill out all fields";
  }

  // only validate if new email different than old one
  if($email != $o_email) {
    $existing_email_result = getUserByEmail($email);
    $existing_email_count = mysqli_num_rows($existing_email_result);

    if($existing_email_count > 0) {
      $errors[] = "The Email you entered already exist";
    }
  }

  if(!empty($errors)) {
    display_errors($errors);
  } else {
    updateUserNameEmail($user_id, $full_name, $email);
    header("location: settings");
  }
}

if(isset($_POST["change_password"])) {
  $errors = array();
  $o_password = sanitize($_POST["o_password"]);
  $n_password = sanitize($_POST["n_password"]);
  $c_password = sanitize($_POST["c_password"]);

  if(empty($o_password) || empty($n_password) || empty($c_password)) {
    $errors[] = "Must fill out all fields";
  }

  if(!password_verify($o_password, $password)) {
    $errors[] = "Incorrect current password";
  }

  if($n_password != $c_password) {
    $errors[] = "Confirmation password doesn't match the password you entered before";
  }

  if(!empty($errors)) {
    display_errors($errors);
  } else {
    $new_password = password_hash($n_password, PASSWORD_DEFAULT);
    updatePassword($user_id, $new_password);
    header("location: settings");
  }
}

?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;Settings</h3>
  </div>
  <div class="panel-body">

    <div class="col-sm-6">
      <div class="panel panel-default">
        <div class="panel-body">
          <form role="form" method="post">
            <div class="form-group">
              <label for="full_name">Full Name</label>
              <input type="text" name="full_name" class="form-control" value="<?=$full_name?>">
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" name="email" class="form-control" value="<?=$email?>">
              <input type="hidden" name="old_email" class="form-control" value="<?=$email?>">
            </div>
            <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
          </form>
        </div> <!-- panel body -->
      </div> <!-- panel -->
    </div>  <!-- end col-sm-6 -->

    <div class="col-sm-6">
      <div class="panel panel-default">
        <div class="panel-body">
          <form role="form" method="post">
            <div class="form-group">
              <label for="full_name">Old password</label>
              <input type="password" name="o_password" class="form-control" placeholder="Enter your current password" value="<?=$o_password?>">
            </div>
            <div class="form-group">
              <label for="full_name">New password</label>
              <input type="password" name="n_password" class="form-control" placeholder="Enter new password" value="<?=$n_password?>">
            </div>
            <div class="form-group">
              <label for="full_name">Confirm New Password</label>
              <input type="password" name="c_password" class="form-control" placeholder="Reenter new password" value="<?=$c_password?>">
            </div>
            <button type="submit" name="change_password" class="btn btn-success">Change Password</button>
          </form>
        </div>
      </div> <!-- panel -->
    </div> <!-- end col-sm-6 -->
  </div> <!-- panel body -->
</div> <!-- panel -->

<?php
// ------------------------------------------------------------------------------------ //
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/admin/includes/footer.php";
?>
