<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/header.php";
if(!is_logged_in()) {
  error_login_redirect();
}
elseif(!(is_permission("hod"))) {
  error_permission_redirect();
}

?>

<div class="row">
  <div class="col-sm-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Ongoing Survey for <?=$_SESSION["department_name"]?> Subjects</h3>
      </div>
      <div class="panel-body" id="view_ongoing_survey_panel_body">
        <?php
        $ongoing_survey_query_result = getOngoingSurvey();
        $count = mysqli_num_rows($ongoing_survey_query_result);
        $ongoing_survey = mysqli_fetch_assoc($ongoing_survey_query_result);
        if($count == 0) {
          echo "<p class='text-center lead text-grey'>There is no ongoing survey</p>";
        } else { ?>
          <canvas id='ongoing_survey_chart' height="100"></canvas>
          <script type="text/javascript">
          $(document).ready(function() {
            var ongoing_id = <?=$ongoing_survey["id"] ?>;
            var ongoing_session = "<?=$ongoing_survey["session"] ?>";
            var department_id = "<?=$_SESSION["department_id"]?>";
            getOngoingSurvey(ongoing_id, ongoing_session, department_id);
          });
          </script>
          <hr>
          <h4 class="text-center">
            Survey Close: <?= pretty_date($ongoing_survey["close_datetime"]);?>
          </h4>
          <?php
        } // end count ?>
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
        $comments_result = getCommentsByDepartment($_SESSION["department_id"]);
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
