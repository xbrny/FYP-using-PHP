<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/admin/includes/header.php";
if(!is_logged_in()) {
  error_login_redirect();
}
elseif(!(is_permission("admin"))) {
  error_permission_redirect();
}

$student_count_result = getStudentCount();
$student_count = mysqli_fetch_assoc($student_count_result);
$lecturer_count_result = getLecturerCount();
$lecturer_count = mysqli_fetch_assoc($lecturer_count_result);

?>
<div class="row">

  <div class="col-sm-6 text-center">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Total Number of User</h3>
      </div>
      <div class="panel-body text-grey">
        <h3>
          <span class="label label-default"><?=$student_count["total"]?> Student </span> &nbsp;&nbsp;
          <span class="label label-default"><?=$lecturer_count["total"]?> Lecturer </span>
        </h3>
      </div>
    </div>
  </div> <!-- end col-sm-3 -->

  <div class="col-sm-6 text-center">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Action</h3>
      </div>
      <div class="panel-body mb-5">
        <a href="users" class="btn btn-custom mr-3">Create new user</a>
        <a href="departments" class="btn btn-custom mr-3">Create new department</a>
        <a href="surveys?action=create" class="btn btn-custom create-new-survey">Create new Survey</a>
      </div>
    </div>
  </div> <!-- end col-sm-3 -->


  <?php
  $department_result = getDepartments();
  while($departments = mysqli_fetch_assoc($department_result)) :
    ?>
    <div class="col-sm-3 text-center">
      <div class="panel panel-default" style="width:250px;">
        <div class="panel-heading">
          <h3 class="panel-title"><?=$departments["name"]?></h3>
        </div>
        <div class="panel-body text-grey">
          <div class="bb">
            <strong>Number of Student</strong> <br>
            <strong class="text-lg">
              <?php
              $student_count_result = getStudentsCountByDepartment($departments["id"]);
              $student_count = mysqli_fetch_assoc($student_count_result);
              echo $student_count["total"];
              ?>
            </strong>
          </div>
          <div class="mt-3">
            <strong>Number of Lecturer</strong> <br>
            <strong class="text-lg">
              <?php
              $lecturer_count_result = getLecturersCountByDepartment($departments["id"]);
              $lecturer_count = mysqli_fetch_assoc($lecturer_count_result);
              echo $lecturer_count["total"];
              ?>
            </strong>
          </div>
        </div>
      </div>
    </div> <!-- end col-sm-3 -->
    <?php
  endwhile;
  ?>

  <div class="col-sm-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Ongoing Survey for all Subjects</h3>
      </div>
      <div class="panel-body" id="view_ongoing_survey_panel_body">
        <?php
        $ongoing_survey_query_result = getOngoingSurvey();
        $count = mysqli_num_rows($ongoing_survey_query_result);
        $ongoing_survey = mysqli_fetch_assoc($ongoing_survey_query_result);
        if($count == 0) {
          echo "<p class='text-center lead'>There is no ongoing survey</p>";
        } else { ?>
          <canvas id='ongoing_survey_chart' height="150"></canvas>
          <script type="text/javascript">
          $(document).ready(function() {
            var ongoing_id = <?=$ongoing_survey["id"] ?>;
            var ongoing_session = "<?=$ongoing_survey["session"] ?>";
            var department_id = "<?=$_SESSION["department_id"]?>";
            getOngoingSurvey(ongoing_id, ongoing_session);
          });
          </script>
          <hr>
          <h4 class="text-center">
            Survey will be closed at on <?= pretty_date($ongoing_survey["close_datetime"]);?>
            <a href="surveys?close=<?=$ongoing_survey["id"]?>" class="btn btn-danger btn-xs pull-right close_survey_button">Close Survey</a>
          </h4>
          <?php
        } // end count ?>
      </div> <!-- end panel-body -->
    </div> <!-- end panel -->
  </div>


</div> <!-- end row -->
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/admin/includes/footer.php";
?>
<script type="text/javascript">


function getOngoingSurvey(ongoing_survey_id, session){
  $.ajax({
    url: "/<?=APP_NAME?>/fetch_survey_data_admin.php",
    method: "post",
    data: {survey_id: ongoing_survey_id},
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
} // end onGoingSurvey function

$(".create-new-survey").click(function() {
  var answer = confirm("Creating new survey will close any previous ongoing survey at this moment, are you sure you want to continue?");
  if (answer == true) {
    return true;
  } else {
    return false;
  }
});
</script>
