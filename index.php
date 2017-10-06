<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/header.php";
check_home_redirect();
if($_POST) {
  $login_id = sanitize($_POST["id"]);
  $password = sanitize($_POST["password"]);
  $errors = array();

  if(empty($login_id) || empty($password)) {
    $errors[] = "ID and password field cannot be empty";
  } else {
    $user_results = getUserLogin($login_id);
    $user = mysqli_fetch_assoc($user_results);
    $count = mysqli_num_rows($user_results);

    if($count < 1) {
      $errors[] = "Login ID entered doesn't exist in record";
    }
    elseif (!password_verify($password, $user["password"])) {
      $errors[] = "Invalid password";
    }
  } // end if empty

  if(!empty($errors)) {
    display_errors($errors);
  } else {
    $id = $user["id"];
    login($id);
  }
}
?>
<div class="row full-page">
  <div class="tagline text-left col-sm-8">
    <h1>Elective Subject Information System</h1>
    <ul class="index-list">
      <li class="text-grey">Get updated information on elective subjects</li>
      <li class="text-grey">Interact with lecturer who teaches elective subject</li>
      <li class="text-grey">Vote for elective subject you want to register for next semester</li>
    </ul>
  </div>
  <div class="col-sm-4 login-div">
    <div class="login-panel panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title text-center">Login to ESIS</h3>
      </div>
      <div class="panel-body">
        <form action="index" method="post">
          <fieldset>
            <div class="form-group">
              <input type="text" class="form-control" placeholder="User ID" name="id" value="<?=sanitize(@$_POST["id"])?>" id="id" autofocus>
            </div>
            <div class="form-group">
              <input type="password" class="form-control" placeholder="Password" name="password" value="<?=sanitize(@$_POST["password"])?>" id="password">
            </div>
            <div class="text-center">
              <input type="submit" class="btn btn-lg btn-custom btn-block" value="Login">
              <small class="help-block">Contact <a target="_blank" href="mailto:admin@example.com">administrator</a> if you have login issue</small>
            </div>
          </fieldset>
        </form>
      </div> <!-- end panel body -->
    </div> <!-- end panel -->
  </div> <!-- end login-div -->
</div> <!-- end row -->
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/footer.php";
?>
