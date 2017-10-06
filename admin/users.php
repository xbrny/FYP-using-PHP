<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/admin/includes/header.php";
if(!is_logged_in()) {
  error_login_redirect();
}
elseif(!(is_permission("admin"))) {
  error_permission_redirect();
}

$full_name = "";
$email = "";
$login_id = "";
$permission = "";
$department = "";
$major = "";

if(isset($_GET["delete"])) {
  $id = sanitize(@$_GET["delete"]);
  deleteUser($id);
  header("Location: users");
}

if(isset($_GET["reset"])) {
  $id = sanitize(@$_GET["reset"]);
  $user_result = getUser($id);
  $user = mysqli_fetch_assoc($user_result);
  resetPassword($id, $user["email"]);
  header("Location: users");
}

if(isset($_GET["edit"])) {
  $id = sanitize($_GET["edit"]);
  $user_query_result = getUser($id);
  $user = mysqli_fetch_assoc($user_query_result);
  $full_name = $user["full_name"];
  $email = $user["email"];
  $login_id = $user["login_id"];
  $permission = $user["permission"];
  $department = $user["department_id"];
  $major = $user["major_id"];
}

if($_POST) :
  $errors = array();
  $full_name = sanitize($_POST["full_name"]);
  $email = sanitize($_POST["email"]);
  $login_id = strtoupper(sanitize($_POST["user_id"]));
  $permission = sanitize($_POST["permission"]);
  $field_names = array('full_name', "email", "user_id", "permission", "department", "major");

  $existing_login_id_result = getUserByLoginID($login_id);
  $existing_email_result = getUserByEmail($email);
  $existing_login_id_count = mysqli_num_rows($existing_login_id_result);
  $existing_email_count = mysqli_num_rows($existing_email_result);

  if(!isset($_GET["edit"])) {
    if($existing_login_id_count > 0) {
      $errors[] = "The Login ID you entered already exist";
    } elseif($existing_email_count > 0) {
      $errors[] = "The Email you entered already exist";
    }
  }

  if($permission == "admin") {
    $_POST["department"] = 0;
    $_POST["major"] = 0;
    $field_names = array('full_name', "email", "user_id", "permission");
  } elseif($permission == "lecturer" || $permission == "hod") {
    $_POST["major"] = 0;
    $field_names = array('full_name', "email", "user_id", "permission", "department");
  }
  $major = sanitize(@$_POST["major"]);
  $department = sanitize(@$_POST["department"]);

  foreach ($field_names as $field) {
    if(empty($_POST["$field"])) {
      $errors[] = "Must fill out all fields";
      break;
    }
  }

  if(!empty($errors)) {
    display_errors($errors);
  } else {
    if(isset($_GET["edit"])) {
      $edit_id = $_GET["edit"];
      updateUser($edit_id, $full_name, $email, $login_id, $permission, $department, $major);
      header("Location: users");
    } else {
      $password = mt_rand(100000,999999);
      $password_hash = password_hash($password, PASSWORD_DEFAULT);
      $email_content = "Send to: $email. Hi $full_name, here is your temporary password to login into ESIS: $password\n";
      $user_email_file = fopen(BASE_URL."/email_file/user_email.txt", "a") or die("Unable to open file");
      fwrite($user_email_file, $email_content);
      fclose($user_email_file);
      createUser($full_name, $email, $login_id, $password_hash, $permission, $department, $major);
      header("Location: users");
    }
  }
endif; // end if $_POST
?>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;<?=((isset($_GET["edit"]))? "Edit" : "Add new")?> user</h3>
  </div>
  <div class="panel-body">
    <form class="form" action="users<?=((isset($_GET["edit"]))? "?edit=".$_GET["edit"] : "")?>" method="post">
      <div class="form-group col-sm-2">
        <label class="sr-only" for="user_id">User ID</label>
        <input type="text" class="form-control" id="user_id" name="user_id" placeholder="User ID" pattern="[A-Za-z]{2}[0-9]{6,8}" value="<?= $login_id ?>">
      </div>
      <div class="form-group col-sm-5">
        <label class="sr-only" for="full_name">Full Name</label>
        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" pattern="[\w\s\.\/@]{3,100}" value="<?= $full_name ?>">
      </div>
      <div class="form-group col-sm-5">
        <label class="sr-only" for="email">Full Name</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= $email ?>">
      </div>
      <div class="form-group col-sm-4">
        <label class="sr-only" for="permission">Permission</label>
        <select class="form-control" name="permission">
          <option value="">----- Select user role -----</option>
          <option value="student" <?=(($permission=="student")? "selected" : "")?>>Student</option>
          <option value="lecturer" <?=(($permission=="lecturer")? "selected" : "")?>>Lecturer</option>
          <option value="hod" <?=(($permission=="hod")? "selected" : "")?>>HOD</option>
          <option value="admin" <?=(($permission=="admin")? "selected" : "")?>>Admin</option>
        </select>
      </div>

      <div class="form-group col-sm-4" id="department_input">
        <label class="sr-only" for="permission">Department</label>
        <select class="form-control" name="department">
          <option value="">----- Select department -----</option>
          <?php
          $departments_result = getDepartments();
          while($departments = mysqli_fetch_assoc($departments_result)) : ?>
          <option value="<?=$departments["id"]?>" <?=(($department==$departments["id"])? "selected" : "")?>><?=$departments["name"]?></option>
          <?php
        endwhile;
        ?>
      </select>
    </div>

    <div class="form-group col-sm-4" id="major_input">
      <label class="sr-only" for="major">Major</label>
      <select class="form-control" name="major">
        <option value="">----- Select major -----</option>
        <?php
        $majors_result = getMajors();
        while($majors = mysqli_fetch_assoc($majors_result)) : ?>
        <option value="<?=$majors["id"]?>" <?=(($major==$majors["id"])? "selected" : "")?>><?=$majors["name"]?></option>
        <?php
      endwhile;
      ?>
    </select>
  </div>
  <div class="form-group col-sm-12">
    <?php if(isset($_GET["edit"])) {
      echo '<a href="users" class="btn btn-default mr-2">Cancel</a>';
      echo "<a href='users?reset=".$id."' class='btn btn-warning reset-user-password'>Reset user password</a>";
    }
    ?>
    <input type="submit" class="btn btn-success" value="<?=((isset($_GET["edit"]))? "Edit" : "Add new")?> User">
  </div>
</form>
</div> <!-- end panel-body -->
</div> <!-- end panel -->

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;User data</h3>
  </div>
  <div class="panel-body">
    <table class="table table-bordered table-striped table-condensed display" id="user_table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Permission</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $users_query_result = getUsers();
        while($users = mysqli_fetch_assoc($users_query_result)) :
          if($users["id"] == $user_id) {
            continue;
          }
          ?>
        <tr>
          <td><?= $users["login_id"] ?></td>
          <td><?= $users["full_name"] ?></td>
          <td><?= $users["email"] ?></td>
          <td><?= ucfirst($users["permission"]); ?></td>
          <td>
            <a href="users?edit=<?= $users["id"] ?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
            <a href="users?delete=<?= $users["id"] ?>" class="btn btn-danger btn-xs delete-btn"><span class="glyphicon glyphicon-remove"></span></a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div> <!-- end panel-body -->
</div> <!-- end panel -->

<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/admin/includes/footer.php";
?>
<script type="text/javascript">
$(document).ready(function() {

  $(".delete-btn").click(function() {
    if(confirm("Are you sure you want to delete this user") == true) {
      return true;
    } else {
      return false;
    }
  });

  $(".reset-user-password").click(function() {
    if(confirm("Are you sure you want to reset this user password?") == true) {
      return true;
    } else {
      return false;
    }
  });

  $('#user_table').DataTable();

  $("select[name=permission]").change(function(){
    var permission = $(this).val();
    if(permission=="admin") {
      $("#department_input").hide(500);
      $("#major_input").hide(500);
    } else if(permission=="lecturer" || permission=="hod" ) {
      $("#major_input").hide(500);
    } else {
      $("#department_input").show(500);
      $("#major_input").show(500);
    }
  }); //end select permission

  var default_permission = $("select[name=permission]").val();
  if(default_permission=="admin") {
    $("#department_input").hide(500);
    $("#major_input").hide(500);
  } else if(default_permission=="lecturer" || default_permission=="hod" ) {
    $("#major_input").hide(500);
  } else {
    $("#department_input").show(500);
    $("#major_input").show(500);
  }

}); // end document ready
</script>
