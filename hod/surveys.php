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

?>
<!-- ############################### VIEW SURVEY ############################### -->
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
    <h3 class="panel-title"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Survey Result</h3>
  </div>
  <div class="panel-body" id="view_survey_panel_body">
    <form class="form-horizontal" method="post" id="view_surveys_form">
      <div class="form-group">
        <label for="session" class="col-sm-2 control-label">Session</label>
        <div class="col-sm-8">
          <select class="form-control" name="survey_id" id="survey_id">
            <?php
            $surveys_query_result = getPastSurveys();
            while($surveys = mysqli_fetch_assoc($surveys_query_result)) : ?>
            <option value="<?=$surveys["id"]?>"><?=ucfirst($surveys["session"])?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-sm-2">
        <input type="submit" name="survey_result" class="btn btn-default btn-block" value="View">
      </div>
    </div>
  </form>
  <div class="survey_chart_div">
    <!-- <canvas id="survey_chart" height="100"></canvas> -->
  </div>
  <button type="button" class="pull-right btn btn-custom print-btn mt-3" name="button">Print</button>
</div> <!-- end panel-body -->
</div> <!-- end panel -->

<?php
// ------------------------------------------------------------------------------------ //
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/footer.php";
?>

<script>
$(document).ready(function() {
  Chart.defaults.global.legend.display = false;
  Chart.defaults.global.elements.rectangle.borderWidth = 2;

  $(".print-btn").click(function() {
    window.print();
  });

  $("#view_surveys_form").submit(function(event){
    event.preventDefault();
    var department_id = <?=$_SESSION["department_id"]?>;
    $(".survey_chart_div").html("<canvas id='survey_chart' height='100'></canvas>");
    var survey_id = $("#view_surveys_form #survey_id").val();
    $.ajax({
      url: "/<?=APP_NAME?>/fetch_survey_data.php",
      method: "post",
      data: {survey_id: survey_id, department_id: department_id},
      success: function(data) {
        var survey = JSON.parse(data);
        var labels = new Array();
        var votes = new Array();
        // console.log(survey[1].subject_name);
        for (var i = 0; i < survey.length; i++) {
          labels.push(survey[i].subject_name);
          votes.push(survey[i].vote);
        }
        var ctx = document.getElementById('survey_chart').getContext('2d');
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
              display: false,
              text: 'Survey result'
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
        }); // end mychart
      }, // end success
      error: function() {
        alert("Error ajax request");
      }
    }); // end ajax
  }); // end view survey form
}); // close document.ready

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
} // end onGoingSurvey function
</script>
