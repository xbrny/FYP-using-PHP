<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/admin/includes/header.php";
if(!is_logged_in()) {
  error_login_redirect();
}
elseif(!(is_permission("admin"))) {
  error_permission_redirect();
}

$department_name = "";
$hod = "";

if(isset($_GET["delete"])) {
  $delete_id = sanitize($_GET["delete"]);
  deleteDepartment($delete_id);
  header("location: departments");
}

if(isset($_GET["edit"])) {
  $edit_id = sanitize($_GET["edit"]);
  $department_result = getDepartment($edit_id);
  $department = mysqli_fetch_assoc($department_result);
  $department_name = $department["name"];
  $department_id = $department["id"];
  $user_d_result = getHodByDepartment($department_id);
  $user_d = mysqli_fetch_assoc($user_d_result);
  $hod = $user_d["id"];
}

if($_POST) {
  $errors = array();
  $department_name = sanitize($_POST["department_name"]);
  $hod = sanitize($_POST["hod"]);
  if(empty($department_name) || empty($hod)) {
    $errors[] = "Must fill out all fields";
  }
  if(!empty($errors)) {
    display_errors($errors);
  } else {
    $new_id = createDepartment($department_name);
  }
}
?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;<?=((isset($_GET["edit"]))? "Edit" : "Add new")?> department</h3>
  </div>
  <div class="panel-body">
    <form class="form" action="departments<?=((isset($_GET["edit"]))? "?edit=".$_GET["edit"] : "")?>" method="post">
      <div class="form-group col-sm-5">
        <label class="sr-only" for="department_name">Department Name</label>
        <input type="text" class="form-control" id="department_name" name="department_name" value="<?=$department_name?>" placeholder="Department Name">
      </div>
      <div class="form-group col-sm-4">
        <label class="sr-only" for="department_name">Department Name</label>
        <select class="form-control" name="hod">
          <option value="">------- Select Head of Department -------</option>
          <?php
          $hods_result = getUserByPermission("hod");
          while($hods = mysqli_fetch_assoc($hods_result)) : ?>
            <option value="<?=$hods["id"]?>" <?=(($hods["id"]==$hod)? "selected" : "")?>><?=$hods["full_name"]?></option>
          <?php
          endwhile; ?>
        </select>
      </div>
      <div class="form-group col-sm-3">
        <?php if(isset($_GET["edit"])) : ?>
          <a href="departments" class="btn btn-default">Cancel</a>
        <?php endif; ?>
        <input type="submit" class="btn btn-success" value="<?=((isset($_GET["edit"]))? "Edit" : "Add")?> Department">
      </div>
    </form>
  </div> <!-- end panel-body -->
</div> <!-- end panel -->

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;Departments data</h3>
  </div>
  <div class="panel-body">
    <table class="table table-bordered table-condensed display" id="department_table">
      <tbody>
        <?php
        $departments_query_result = getDepartments();
        while($departments = mysqli_fetch_assoc($departments_query_result)) :
          $subjects_d_query = getSubjectsByDepartment($departments["id"]); ?>
          <tr>
            <td colspan="3" class="text-center bg-default">
              <strong><?= $departments["name"] ?></strong>
              <a href="departments?delete=<?= $departments["id"] ?>" class="btn btn-danger btn-xs pull-right delete-btn"><span class="glyphicon glyphicon-remove"></span></a>
              <a href="departments?edit=<?= $departments["id"] ?>" class="btn btn-default btn-xs pull-right mr-2"><span class="glyphicon glyphicon-pencil"></span></a>
            </td>
          </tr>
          <?php while($subjects_d = mysqli_fetch_assoc($subjects_d_query)) : ?>
            <tr>
              <td width="15%"><?= $subjects_d["code"] ?></td>
              <td><?= $subjects_d["name"] ?></td>
              <td>
                <?php
                $teaching_l_q = getTeachingLecturer($subjects_d["id"]);
                $teaching_l = mysqli_fetch_assoc($teaching_l_q);
                echo $teaching_l["full_name"];
                ?>
              </td>
            </tr>
          <?php endwhile; ?>
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
    $(".delete-btn").click(function(){
      if(confirm("Are you sure you want to delete this departments") == true) {
        return true;
      } else {
        return false;
      }
    });
  }); // end document ready
</script>
