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

if(isset($_POST["add_major"])) {
  $major_name = sanitize($_POST["name"]);
  $department_id = $_SESSION["department_id"];

}

if(isset($_GET["remove"])) {
  $subject_id = sanitize($_GET["remove"]);
}

if(isset($_GET["add_subject"])) {
  $subject_id = sanitize($_GET["add_subject"]);
}

?>

<div class="row">
  <div class="col-sm-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th-list"></span>&nbsp;&nbsp;Course Structure For Major</h3>
      </div>
      <div class="panel-body">
        <form class="form-horizontal" method="post" id="major_structure_form">
          <div class="form-group">
            <label for="major" class="control-label col-sm-1">Major</label>
            <div class="col-sm-9">
              <select class="form-control" name="major" id="major_input">
                <?php
                $majors_d_result = getMajorsByDepartment($_SESSION["department_id"]);
                while($major_d = mysqli_fetch_assoc($majors_d_result)) :
                  ?>
                  <option value="<?=$major_d["id"]?>"><?=$major_d["name"]?></option>
                  <?php
                endwhile;
                ?>
              </select>
            </div>
            <div class="col-sm-2">
              <input type="submit" name="view_major" value="View" class="btn btn-default btn-block">
            </div>
          </div>
        </form>
        <div>
          <table class="table table-bordered" id="subject_major_tables">

          </table>
        </div>
      </div>
    </div>
  </div> <!-- end col-sm-12 -->

  <div class="col-sm-5">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th-list"></span>&nbsp;&nbsp;Majors for <?=$_SESSION["department_name"]?></h3>
      </div>
      <div class="panel-body">
        <form class="form-horizontal" method="post">
          <div class="form-group">
            <div class="col-sm-9">
              <input type="text" name="major_name" placeholder="Enter major you want to add" class="form-control">
            </div>
            <div class="col-sm-3">
              <input type="submit" name="add_major" value="Add" class="btn btn-success btn-block">
            </div>
          </div>
        </form>
        <div>
          <table class="table mt-5" id="subject_major_tables">
            <?php
            $majors_d_result = getMajorsByDepartment($_SESSION["department_id"]);
            while($major_d = mysqli_fetch_assoc($majors_d_result)) :
              ?>
              <tr>
                <td><?=$major_d["name"]?></td>
              </tr>
              <?php
            endwhile;
            ?>
          </table>
        </div>
      </div>
    </div>
  </div> <!-- end col-sm-12 -->
</div> <!-- end row -->

<div class="modal fade" id="add_subject">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add subject to major</h4>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-condensed display" id="department_table">
          <tbody>
            <?php
            $departments_query_result = getDepartments();
            while($departments = mysqli_fetch_assoc($departments_query_result)) :
              $subjects_d_query = getSubjectsByDepartment($departments["id"]); ?>
              <tr>
                <td colspan="3" class="text-center bg-default">
                  <strong><?= $departments["name"] ?></strong>
                </td>
              </tr>
              <?php
              while($subjects_d = mysqli_fetch_assoc($subjects_d_query)) :
                ?>
                <tr>
                  <td width="15%"><?= $subjects_d["code"] ?></td>
                  <td width="70%"><a href="subject?id=<?= $subjects_d["id"] ?>"><?= $subjects_d["name"] ?></a></td>
                  <td width="15%" class="text-center"><a href="subjects_majors?add_subject=<?=$subjects_d["id"]?>" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-plus-sign"></span></a></td>
                </tr>
                <?php
              endwhile;
              ?>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div> <!-- modal content -->
  </div> <!-- modal-dialog -->
</div> <!-- modal -->

<?php
// ------------------------------------------------------------------------------------ //
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/footer.php";
?>

<script type="text/javascript">

function getSubjectByMajor() {
  var major_subject_id = $("select[name=major]").val();
  $.ajax({
    url: "/<?=APP_NAME?>/hod/fetch_subjects_majors.php",
    method: "post",
    data: {major: major_subject_id},
    success: function(data) {
      $("#subject_major_tables").html(data);
    },
    errors: function() {
      alert("AJAX call failed");
    }
  });
} // end function

function addSubject(major_id) {
  $("#add_subject").modal("show");
}

$(document).ready(function() {

  getSubjectByMajor();

  $("#major_structure_form").submit(function(e) {
    e.preventDefault();
    getSubjectByMajor();
  }); // end major structure form
});
</script>
