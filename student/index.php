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
    header("Location: index");
  }
}
?>

<div class="row">
  <div class="col-sm-7">
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
                        <label class="col-sm-9">
                          <input type="radio" name="subject" value="<?=$subjects["id"]?>"><span><?=$subjects["name"]?></span>
                        </label>
                        <div class="col-sm-3">
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

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Top 3 voted subjects for past three semesters</h3>
      </div>
      <div class="panel-body">
        <table class="table table-bordered">
          <?php
          $latest_survey_result = getLatestSurveys();
          while($latest_survey = mysqli_fetch_assoc($latest_survey_result)) :
            $top_voted_subject_result = getTopVotedSubject($latest_survey["id"], $_SESSION["department_id"]);
            ?>
            <tr>
              <th colspan="2" class="text-center bg-default"><?=$latest_survey["session"];?></th>
            </tr>
            <?php
            while($top_voted_subject = mysqli_fetch_assoc($top_voted_subject_result)) :
              ?>
              <tr>
                <td width="80%"><a href="../subject?id=<?=$top_voted_subject["id"];?>"><?=$top_voted_subject["name"];?></a></td>
                <td class="text-center"><span class="label label-primary"><?=$top_voted_subject["vote"];?></span></td>
              </tr>
              <?php
            endwhile;
          endwhile;
          ?>
        </table>
      </div> <!-- end panel-body -->
    </div> <!-- end panel -->
  </div> <!-- end col-sm-7 -->

  <div class="col-sm-5">

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-comment"></span>&nbsp;&nbsp;Latest Comments</h3>
      </div>
      <div class="panel-body">
        <?php
        $comments_result = getComments();
        while($comments = mysqli_fetch_assoc($comments_result)) :
          $subject_result = getSubject($comments["subject_id"]);
          $subject = mysqli_fetch_assoc($subject_result);
          ?>
          <div class="panel panel-default">
            <div class="panel-body">
              <p class="text-justify comment-content">
                <?= $comments["content"]?>
              </p>
            </div> <!-- end panel body -->
            <div class="panel-footer">
              <div class="clearfix">
                <small>Date posted: <?=pretty_date($comments["posted_at"])?></small><br>
                <small>Subject: <a href="/<?=APP_NAME?>/subject.php?id=<?=$subject["id"]?>" target="_blank"><?= $subject["name"] ?></a></small>
              </div>
            </div> <!-- end panel footer -->
          </div> <!-- end panel -->
          <?php
        endwhile;
        ?>
      </div> <!-- end panel-body -->
    </div> <!-- end panel -->
  </div> <!-- end col-sm-5 -->

</div> <!-- row -->


<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/footer.php";
?>

<script>
$(document).ready(function() {
  Chart.defaults.global.legend.display = false;
  Chart.defaults.global.elements.rectangle.borderWidth = 2;

  $(".comment-content").readmore({
    lessLink: '<a href="#">Read less</a>',
  });

});

function getOngoingSurvey(ongoing_survey_id, session, department_id){
  $.ajax({
    url: "/<?=APP_NAME?>/fetch_survey_data.php",
    method: "post",
    data: {survey_id: ongoing_survey_id, department_id: department_id},
    success: function(data) {
      var survey = JSON.parse(data);
      var labels = new Array();
      var votes = new Array();
      // console.log(survey[1].subject_name);
      for (var i = 0; i < survey.length; i++) {
        labels.push(survey[i].subject_name);
        votes.push(survey[i].vote);
      }
      Chart.defaults.global.legend.display = false;
      var ctx = document.getElementById('ongoing_survey_chart').getContext('2d');
      var myChart = new Chart(ctx, {
        type: 'horizontalBar',
        data: {
          labels: labels,
          datasets: [{
            label: 'subject',
            data: votes,
            backgroundColor: "rgba(0,0,255,0.3)"
          }]
        }, // close data
        options: {
          title: {
            display: true,
            text: session,
            fontSize: 20,
            fontStyle: 'normal'
          },
          scales: {
            xAxes: [{
              ticks: {
                beginAtZero: true,
                stepSize: 1
              }
            }]
          }
        }
      }); // Chart
    }, // end success
    error: function() {
      alert("Error ajax request");
    }
  });
}
</script>
