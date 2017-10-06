<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/header.php";
// ------------------------------------------------------------------------------------ //
if(!is_logged_in()) {
  error_login_redirect();
}
elseif(!(is_permission("student"))) {
  error_permission_redirect();
}
$ongoing_survey_query_result = getOngoingSurvey();
$ongoing_survey_count = mysqli_num_rows($ongoing_survey_query_result);
$ongoing_survey= mysqli_fetch_assoc($ongoing_survey_query_result);
$subjects_query_result = getSubjectsByMajor($user_data["major_id"]);

if($_POST) {
  $errors = array();
  $survey_id = $ongoing_survey["id"];
  $subject_id = sanitize(@$_POST["subject"]);

  if(empty($subject_id)) {
    $errors[] = "Please select the subject you want to vote";
    display_errors($errors);
  } else {
    insertVote($survey_id, $subject_id);
    updateAnswered($user_id);
    header("Location: surveys");
  }
}
?>
<!-- ############################### VIEW SURVEY ############################### -->
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Elective Subject Survey</h3>
  </div>
  <div class="panel-body">
    <form method="post">
      <table class="table">
        <?php
        if($ongoing_survey_count == 0) {
          echo "<p class='text-center text-grey lead'>There is no ongoing survey</p>";
        } else {
          if($user_data["survey_answered"] == 0) {
            ?>
            <p>Choose one subject that you want to register for <strong><?=$ongoing_survey["session"]?></strong></p>
            <?php while($subjects = mysqli_fetch_assoc($subjects_query_result)) :
              ?>
              <tr>
                <td>
                  <div class="radio">
                    <label class="col-sm-5">
                      <input type="radio" name="subject" value="<?=$subjects["id"]?>"><span><?=$subjects["name"]?></span>
                    </label>
                    <div class="col-sm-2">
                      <a href="/<?=APP_NAME?>/subject?id=<?=$subjects["id"]?>" class="btn btn-custom btn-xs" target="_blank"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> subject detail</a>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <?php
              endwhile;?>
              <td>
                <input type="submit" name="submit_response" class="btn btn-success btn-md btn-block" value="Submit">
                <input type="reset" class="btn btn-default btn-md btn-block">
              </td>
              <?php
            } // end if($user_data["survey_answered"] == 0)
            else {
              echo "<p class='text-center text-grey lead'>Survey already answered</p>";
            }
          } // end if($ongoing_survey_count == 0)
          ?>
        </tr>
      </table>
    </form>
  </div> <!-- end panel-body -->
</div> <!-- end panel -->


<?php
// ------------------------------------------------------------------------------------ //
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/footer.php";
?>
