<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/header.php";
// ------------------------------------------------------------------------------------ //
?>

<div class="row">
  <div class="col-sm-8 <?=((is_logged_in() && is_permission("student"))? "" : "col-sm-offset-2")?>">

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
          <?php while($subjects_d = mysqli_fetch_assoc($subjects_d_query)) : ?>
            <tr>
              <td width="15%" class="text-center"><?= $subjects_d["code"] ?></td>
              <td><a href="subject?id=<?= $subjects_d["id"] ?>"><?= $subjects_d["name"] ?></a></td>
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
  </div>

  <?php if(is_logged_in() && is_permission("student")) : ?>
    <div class="col-sm-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;Subjects Offered For Your Major</h3>
        </div>
        <div class="panel-body">
          <table class="table table-bordered display" id="subject_table">
            <tbody>
              <?php
              $subjects_query_result = getSubjectsByMajor($user_data["major_id"]);
              while($subject = mysqli_fetch_assoc($subjects_query_result)) :
                ?>
                <tr>
                  <td><?=$subject["name"]?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div> <!-- end panel-body -->
      </div> <!-- end panel -->
    </div>
  <?php endif; ?>
</div>

<?php
// ------------------------------------------------------------------------------------ //
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/footer.php";
?>
