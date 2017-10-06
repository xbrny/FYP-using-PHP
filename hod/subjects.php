<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/header.php";
// ------------------------------------------------------------------------------------ //
if(!is_logged_in()) {
  error_login_redirect();
}
elseif(!(is_permission("hod"))) {
  error_permission_redirect();
}
$action = "";
$action = @$_GET["action"];
?>
<!-- ############################### VIEW SUBJECT ############################### -->
<?php if(!isset($_GET["action"])) :
  //-- ############################### CREATE SUBJECT ############################### -->
  if($_POST) {
    $errors = array();
    $code             = sanitize($_POST["code"]);
    $subject_name     = sanitize($_POST["subject_name"]);
    $department_id    = $_SESSION["department_id"];
    $person_in_charge = sanitize($_POST["person_in_charge"]);
    if(empty($code) || empty($subject_name) || empty($department_id) || empty($person_in_charge)) {
      $errors[] = "Must fill out all the fields";
    }

    if(!empty($errors)) {
      display_errors($errors);
    } else {
      createSubject($code, $subject_name, $department_id, $person_in_charge);
      header("Location: subjects");
    }
  }

  ?>

  <div class="row">

    <div class="col-sm-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Subjects belongs to <?= $_SESSION["department_name"] ?></h3>
        </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-condensed">
            <thead>
              <tr>
                <th>Code</th>
                <th>Subject Name</th>
                <th>Person in Charge</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $subjects_query_result = getSubjectsByDepartment($_SESSION["department_id"]);
              while($subjects = mysqli_fetch_assoc($subjects_query_result)) :
                $teaching_lecturer_query = getTeachingLecturer($subjects["id"]);
                $PIC = mysqli_num_rows($teaching_lecturer_query);
                $person_in_charge = mysqli_fetch_assoc($teaching_lecturer_query);
                ?>
                <tr>
                  <td><?= $subjects["code"] ?></td>
                  <td><a href="/<?=APP_NAME?>/subject.php?id=<?=$subjects["id"]?>" target="_blank"><?= $subjects["name"] ?></a></td>
                  <td><?= (($PIC==0)? "Unassigned" : $person_in_charge["full_name"]);?></td>
                  <td>
                    <a href="subjects?action=edit&amp;id=<?= $subjects["id"] ?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="subjects?action=delete&amp;id=<?= $subjects["id"] ?>" class="btn btn-danger btn-xs delete-btn"><span class="glyphicon glyphicon-remove"></span></a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div> <!--panel-body -->
      </div> <!--panel-default -->

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Create New <?= $_SESSION["department_name"] ?> Subject</h3>
        </div>
        <div class="panel-body">
          <form class="" action="subjects" method="post">
            <div class="form-group">
              <label for="" class="control-label">Subject Code</label>
              <input type="text" class="form-control" name="code" pattern="[A-Za-z]{4}[0-9]{3}" value="<?= @$_POST["code"] ?>">
            </div>
            <div class="form-group">
              <label for="" class="control-label">Subject Name</label>
              <input type="text" class="form-control" name="subject_name" value="<?= @$_POST["subject_name"] ?>">
            </div>
            <div class="form-group">
              <label for="" class="control-label">Lecturer in charge</label>
              <select class="form-control" name="person_in_charge">
                <option value=""></option>
                <?php
                $lecturer_query_result = getLecturersByDepartment($_SESSION["department_id"]);
                while($lecturer_d = mysqli_fetch_assoc($lecturer_query_result)) : ?>
                <option value="<?=$lecturer_d["id"]?>" <?= (($lecturer_d["id"]==@$_POST["person_in_charge"])?"selected":"") ?>><?=$lecturer_d["full_name"]?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <input type="submit" class="btn btn-success pull-right" value="Create Subject">
          </div>
        </form>
      </div> <!-- end panel body -->
    </div> <!-- end panel -->
  </div> <!-- col-sm-8 -->


  <div class="col-sm-4">
    <?php
    $departments_query_result = getDepartments();
    while($departments = mysqli_fetch_assoc($departments_query_result)) :
      if($departments["id"] == $_SESSION["department_id"]) {
        continue;
      }
      $subjects_by_department_query_result = getSubjectsByDepartment($departments["id"]);
      ?>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title text-center"><?=$departments["name"]?></h3>
        </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-condensed">
            <tbody>
              <?php while($subjects_d = mysqli_fetch_assoc($subjects_by_department_query_result)) : ?>
                <tr>
                  <td><a href="/<?=APP_NAME?>/subject?id=<?= $subjects_d["id"]?>" target="_blank"><?= $subjects_d["code"] . " - " .$subjects_d["name"] ?></a></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div> <!-- end panel-body -->
      </div> <!-- end panel -->
    <?php endwhile; ?>
  </div> <!-- end col-4 -->

</div> <!-- end row -->
<?php endif; ?>

<!-- ############################### EDIT SURVEY ############################### -->
<?php if(isset($_GET["action"]) && $_GET["action"] == "edit") :
  $edit_id= sanitize($_GET["id"]);
  $subject_edit_query = getSubject($edit_id);
  $subject_edit = mysqli_fetch_assoc($subject_edit_query);
  $selected_pic = $subject_edit["user_id"];
  if($_POST) {
    $errors = array();
    $code             = sanitize($_POST["code"]);
    $subject_name     = sanitize($_POST["subject_name"]);
    $department_id    = $_SESSION["department_id"];
    $person_in_charge = sanitize($_POST["person_in_charge"]);
    $selected_pic     = $person_in_charge;
    if(empty($code) || empty($subject_name) || empty($department_id) || empty($person_in_charge)) {
      $errors[] = "Must fill out all the fields";
    }

    if(!empty($errors)) {
      display_errors($errors);
    } else {
      updateSubject($edit_id, $code, $subject_name, $department_id, $person_in_charge);
      header("Location: subjects");
    }
  }
  ?>

  <div class="row">

    <div class="col-sm-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Subjects belongs to <?= $_SESSION["department_name"] ?></h3>
        </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-condensed">
            <thead>
              <tr>
                <th>Code</th>
                <th>Subject Name</th>
                <th>Person in Charge</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $subjects_query_result = getSubjectsByDepartment($_SESSION["department_id"]);
              while($subjects = mysqli_fetch_assoc($subjects_query_result)) :
                $teaching_lecturer_query = getTeachingLecturer($subjects["id"]);
                $PIC = mysqli_num_rows($teaching_lecturer_query);
                $person_in_charge = mysqli_fetch_assoc($teaching_lecturer_query);
                ?>
                <tr>
                  <td><?= $subjects["code"] ?></td>
                  <td><a href="/<?=APP_NAME?>/subject.php?id=<?=$subjects["id"]?>" target="_blank"><?= $subjects["name"] ?></a></td>
                  <td><?= (($PIC==0)? "Unassigned" : $person_in_charge["full_name"]);?></td>
                  <td>
                    <a href="subjects?action=edit&amp;id=<?= $subjects["id"] ?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="subjects?action=delete&amp;id=<?= $subjects["id"] ?>" class="btn btn-danger btn-xs delete-btn"><span class="glyphicon glyphicon-remove"></span></a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div> <!--panel-body -->
      </div> <!--panel-default -->

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Update <?= $_SESSION["department_name"] ?> Subject</h3>
        </div>
        <div class="panel-body">
          <form action="subjects?action=edit&amp;id=<?=$edit_id?>" method="post">
            <div class="form-group">
              <label for="" class="control-label">Subject Code</label>
              <input type="text" class="form-control" name="code" pattern="[A-Za-z]{4}[0-9]{3}" value="<?= ((isset($_POST["code"]))? $_POST["code"] : $subject_edit["code"]) ?>">
            </div>
            <div class="form-group">
              <label for="" class="control-label">Subject Name</label>
              <input type="text" class="form-control" name="subject_name" value="<?= ((isset($_POST["subject_name"]))? $_POST["subject_name"] : $subject_edit["name"]) ?>">
            </div>
            <div class="form-group">
              <label for="" class="control-label">Lecturer in charge</label>
              <select class="form-control" name="person_in_charge">
                <option value=""></option>
                <?php
                $lecturer_query_result = getLecturersByDepartment($_SESSION["department_id"]);
                while($lecturer_d = mysqli_fetch_assoc($lecturer_query_result)) : ?>
                <option value="<?=$lecturer_d["id"]?>" <?= (($selected_pic==$lecturer_d["id"])? "selected": "") ?>><?=$lecturer_d["full_name"]?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <input type="submit" class="btn btn-success pull-right" value="Update Subject">
            <a href="subjects" class="btn btn-default pull-right mr-3">Cancel</a>
          </div>
        </form>
      </div> <!-- end panel body -->
    </div> <!-- end panel -->
  </div> <!-- col-sm-8 -->


  <div class="col-sm-4">
    <?php
    $departments_query_result = getDepartments();
    while($departments = mysqli_fetch_assoc($departments_query_result)) :
      if($departments["id"] == $_SESSION["department_id"]) {
        continue;
      }
      $subjects_by_department_query_result = getSubjectsByDepartment($departments["id"]);
      ?>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title text-center"><?=$departments["name"]?></h3>
        </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-condensed">
            <tbody>
              <?php while($subjects_d = mysqli_fetch_assoc($subjects_by_department_query_result)) : ?>
                <tr>
                  <td><a href="/<?=APP_NAME?>/subject?id=<?= $subjects_d["id"]?>" target="_blank"><?= $subjects_d["code"] . " - " .$subjects_d["name"] ?></a></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div> <!-- end panel-body -->
      </div> <!-- end panel -->
    <?php endwhile; ?>
  </div> <!-- end col-4 -->

</div> <!-- end row -->
<?php
endif; // end edit survey
?>

<!-- ############################### DELETE SURVEY ############################### -->
<?php if(isset($_GET["action"]) && $_GET["action"] == "delete") : ?>
  <?php
  $id = sanitize(@$_GET["id"]);
  deleteSubject($id);
  header("Location: subjects");
  ?>
<?php endif; ?>

<?php
// ------------------------------------------------------------------------------------ //
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/footer.php";
?>

<script type="text/javascript">
$(document).ready(function() {
  $(".delete-btn").click(function() {
    if(confirm("Are you sure you want to delete this subject?") == true) {
      return true;
    } else {
      return false;
    }
  });
});
</script>
