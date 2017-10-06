<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/admin/includes/header.php";
// ------------------------------------------------------------------------------------ //
if(!is_logged_in()) {
  error_login_redirect();
}
elseif(!(is_permission("admin"))) {
  error_permission_redirect();
}
$surveys_query_result = getPastSurveys();
$action = "";
$action = @$_GET["action"];

if(isset($_GET["close"])) {
  $survey_id = sanitize($_GET["close"]);
  closeSurvey($survey_id);
  header("location: surveys");
}
?>
<!-- ############################### VIEW SURVEY ############################### -->
<?php if(!isset($_GET["action"])) : ?>
  <div class="clearfix mb-3">
    <a href="surveys?action=create" class="btn btn-success pull-right create-new-survey">Create New Survey</a>
  </div>

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
          <button type="button" class="pull-right btn btn-custom btn-xs print-btn mr-2" name="button">Print</button>
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
              <?php while($surveys = mysqli_fetch_assoc($surveys_query_result)) : ?>
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
    </div> <!-- end panel-body -->
  </div> <!-- end panel -->

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;Survey Data</h3>
    </div>
    <div class="panel-body">
      <table class="table table-bordered table-striped table-condensed" id="survey_table">
        <thead>
          <tr>
            <th>Session</th>
            <th>Start</th>
            <th>End</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $surveys_query_result = getSurveys();
          while($surveys = mysqli_fetch_assoc($surveys_query_result)) : ?>
          <tr>
            <td><?= $surveys["session"] ?></td>
            <td><?= pretty_date($surveys["open_datetime"]) ?></td>
            <td><?= pretty_date($surveys["close_datetime"]) ?></td>
            <td>
              <a href="surveys?action=edit&amp;id=<?= $surveys["id"] ?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
              <a href="surveys?action=delete&amp;id=<?= $surveys["id"] ?>" class="btn btn-danger btn-xs delete-btn"><span class="glyphicon glyphicon-remove"></span></a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php endif; ?>

<!-- ############################### CREATE SURVEY ############################### -->
<?php if(isset($_GET["action"]) && $_GET["action"] == "create") : ?>
  <?php

  if($_POST) :
    $errors = array();
    $semester   = sanitize($_POST["semester"]);
    $year1      = sanitize($_POST["year1"]);
    $year2      = sanitize($_POST["year2"]);
    $open_date  = sanitize($_POST["open_date"]);
    $open_time  = sanitize($_POST["open_time"]);
    $close_date = sanitize($_POST["close_date"]);
    $close_time = sanitize($_POST["close_time"]);
    $department = sanitize($_POST["department"]);
    $field_names = array("semester", "year1", "year2", "open_date", "open_time", "close_date", "close_time", "department");
    foreach ($field_names as $field) {
      if(empty($_POST["$field"])) {
        $errors[] = "Must fill out all fields";
        break;
      }
    }

    if(!empty($errors)) {
      display_errors($errors);
    } else {
      $session = "Semester $semester - $year1/$year2";
      $open_datetime  = "$open_date $open_time";
      $close_datetime  = "$close_date $close_time";
      $ongoing_survey_result = getOngoingSurvey();
      if(mysqli_num_rows($ongoing_survey_result)) {
        $ongoing_survey = mysqli_fetch_assoc($ongoing_survey_result);
        closeSurvey($ongoing_survey["id"]);
      }
      createSurvey($session, $open_datetime, $close_datetime, $department);
      $survey_id = mysqli_insert_id($db);
      initializeVotes($survey_id);
      resetAnswered();
      header("Location: surveys");
    }
  endif; // end if $_POST
  ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add new survey</h3>
    </div>
    <div class="panel-body">
      <form class="form-horizontal" action="surveys?action=create" method="post">
        <input type="hidden" name="department" value="<?= 'IS' ?>" >
        <div class="form-group">
          <label for="semester" class="col-sm-2 control-label">Session</label>
          <div class="col-sm-5">
            <select class="form-control" id="semester" name="semester">
              <option value="1" <?= ((sanitize(@$_POST["semester"]) == 1)? "selected":"") ?>>Semester 1</option>
              <option value="2" <?= ((sanitize(@$_POST["semester"]) == 2)? "selected":"") ?>>Semester 2</option>
            </select>
          </div>
          <div class="col-sm-5 form-inline">
            <strong>Year&nbsp;&nbsp;</strong>
            <input type="number" class="form-control year-input" name="year1" value="<?= ((isset($_POST["year1"]) )? @$_POST["year1"]:"2015") ?>"><strong>/</strong>
            <input type="number" class="form-control year-input" name="year2" value="<?= ((isset($_POST["year2"]) )? @$_POST["year2"]:"2016") ?>" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="open_date" class="col-sm-2 control-label">Open the survey</label>
          <div class="col-sm-5">
            <input type="date" name="open_date"  class="form-control" min="<?=date("Y-m-d")?>" id="open_date">
            <!-- <input type="date" name="open_date"  class="form-control" id="open_date" min= date('Y-m-d') > -->
          </div>
          <div class="col-sm-5">
            <input type="time" name="open_time" class="form-control" value="<?php echo date('H:i'); ?>" id="open_time" >
          </div>
        </div>
        <div class="form-group">
          <label for="close_date" class="col-sm-2 control-label">Close the survey</label>
          <div class="col-sm-5">
            <input type="date" name="close_date" class="form-control" min="<?=date("Y-m-d")?>" id="close_date">
            <!-- <input type="date" name="close_date" class="form-control" id="close_date" min= date('Y-m-d') > -->
          </div>
          <div class="col-sm-5">
            <input type="time" name="close_time" class="form-control" value="<?php echo date('H:i'); ?>" id="close_time" >
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <input type="submit" class="btn btn-success pull-right" name="post_survey" value="Create">
            <a href="surveys" class="btn btn-default pull-right mr-3">Cancel</a>
          </div>
        </div>
      </form>
    </div> <!-- end panel-body -->
  </div> <!-- end panel -->
<?php endif; ?>

<!-- ############################### EDIT SURVEY ############################### -->
<?php if(isset($_GET["action"]) && $_GET["action"] == "edit") : ?>
  <?php
  $edit_id = sanitize($_GET["id"]);
  $survey_result_query = getSurvey($edit_id);
  $survey = mysqli_fetch_assoc($survey_result_query);
  $session_array = explode(" ", $survey["session"]);
  $semester   = $session_array[1]; // **************** var
  $year       = $session_array[3]; // **************** var
  $year_array = explode("/", $year);
  $year1      = $year_array[0]; // **************** var
  $year2      = $year_array[1]; // **************** var
  $open_datetime_array = explode(" ", $survey["open_datetime"]);
  $open_date  = $open_datetime_array[0]; // **************** var
  $open_time  = $open_datetime_array[1]; // **************** var
  $close_datetime_array = explode(" ", $survey["close_datetime"]);
  $close_date = $close_datetime_array[0]; // **************** var
  $close_time = $close_datetime_array[1]; // **************** var
  $department = $survey["department"]; // **************** var
  if($_POST) :
    $errors = array();
    $semester   = sanitize($_POST["semester"]);
    $year1      = sanitize($_POST["year1"]);
    $year2      = sanitize($_POST["year2"]);
    $open_date  = sanitize($_POST["open_date"]);
    $open_time  = sanitize($_POST["open_time"]);
    $close_date = sanitize($_POST["close_date"]);
    $close_time = sanitize($_POST["close_time"]);
    $department = sanitize($_POST["department"]);
    $field_names = array("semester", "year1", "year2", "open_date", "open_time", "close_date", "close_time", "department");
    foreach ($field_names as $field) {
      if(empty($_POST["$field"])) {
        $errors[] = "Must fill out all fields";
        break;
      }
    }

    if(!empty($errors)) {
      display_errors($errors);
    } else {
      $session = "Semester $semester - $year1/$year2";
      $open_datetime  = "$open_date $open_time";
      $close_datetime  = "$close_date $close_time";
      updateSurvey($edit_id, $session, $open_datetime, $close_datetime, $department);
      if($close_datetime > date("Y-m-d H:i:s")) {
        resetAnswered();
      }
      header("Location: surveys");
    }
  endif; // end if $_POST
  ?>
  <h2 class="text-center">Edit Survey</h2><hr>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="text-center">Edit survey for Information System Student</h4>
    </div>
    <div class="panel-body">
      <form class="form-horizontal" action="surveys?action=edit&amp;id=<?= $edit_id ?>" method="post">
        <input type="hidden" name="department" value="<?= $department ?>" >
        <div class="form-group hidden">
          <label for="semester" class="col-sm-2 control-label">Session</label>
          <div class="col-sm-5">
            <select class="form-control" id="semester" name="semester">
              <option value="1" <?= (($semester==1)? "selected":"") ?>>Semester 1</option>
              <option value="2" <?= (($semester==2)? "selected":"") ?>>Semester 2</option>
            </select>
          </div>
          <div class="col-sm-5 form-inline">
            <strong>Year&nbsp;&nbsp;</strong>
            <input type="number" class="form-control year-input" name="year1" value="<?= $year1 ?>"><strong>/</strong>
            <input type="number" class="form-control year-input" name="year2" value="<?= $year2 ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="semester" class="col-sm-2 control-label">Session</label>
          <div class="col-sm-5">
            <input type="text" class="form-control" value="Semester <?=$semester?>" readonly>
          </div>
          <div class="col-sm-5 form-inline">
            <strong>Year&nbsp;&nbsp;</strong>
            <input type="number" class="form-control year-input" value="<?= $year1 ?>" readonly><strong>/</strong>
            <input type="number" class="form-control year-input" value="<?= $year2 ?>" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="open_date" class="col-sm-2 control-label">Open the survey</label>
          <div class="col-sm-5">
            <input type="date" name="open_date" class="form-control" id="open_date" min="<?= date('Y-m-d'); ?>" value="<?=date("Y-m-d", strtotime($open_date))?>">
          </div>
          <div class="col-sm-5">
            <input type="time" name="open_time" class="form-control" id="open_time" value="<?=$open_time?>">
          </div>
        </div>
        <div class="form-group">
          <label for="close_date" class="col-sm-2 control-label">Close the survey</label>
          <div class="col-sm-5">
            <input type="date" name="close_date" class="form-control" id="close_date" min="<?= date('Y-m-d'); ?>" value="<?=date("Y-m-d", strtotime($close_date))?>">
          </div>
          <div class="col-sm-5">
            <input type="time" name="close_time" class="form-control" id="close_time" value="<?=$close_time?>">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <input type="submit" class="btn btn-success pull-right" name="post_survey" value="Update">
            <a href="surveys" class="btn btn-default pull-right mr-3">Cancel</a>
          </div>
        </div>
      </form>
    </div> <!-- end panel-body -->
  </div> <!-- end panel -->
<?php endif; ?>

<!-- ############################### DELETE SURVEY ############################### -->
<?php if(isset($_GET["action"]) && $_GET["action"] == "delete") :
  $id = sanitize(@$_GET["id"]);
  deleteSurvey($id);
  header("Location: surveys");
endif; ?>

<?php
// ------------------------------------------------------------------------------------ //
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/admin/includes/footer.php";
?>

<script>
$(document).ready(function() {
  Chart.defaults.global.legend.display = false;
  Chart.defaults.global.elements.rectangle.borderWidth = 2;

  // document.getElementById('open_date').valueAsDate = new Date();
  // document.getElementById('close_date').valueAsDate = new Date();
  $("input[name=year1]").change(function() {
    // alert($(this).val());
    $("input[name=year2]").val(parseInt($(this).val()) + 1);
  });


  $(".print-btn").click(function() {
    window.print();
  });

  $(".delete-btn").click(function() {
    if(confirm("Are you sure you want to delete this survey and all its votes?") == true) {
      return true;
    } else {
      return false;
    }
  });

  $(".close_survey_button").click(function() {
    if (confirm("Are you sure you want to close this survey?") == true) {
      return true;
    } else {
      return false;
    }
  });

  $(".create-new-survey").click(function() {
    var answer = confirm("Creating new survey will close any previous ongoing survey at this moment, are you sure you want to continue?");
    if (answer == true) {
      return true;
    } else {
      return false;
    }
  });

  $("#survey_table").DataTable( {
    "aaSorting": []
  });

  $("#view_surveys_form").submit(function(event){
    event.preventDefault();
    $(".survey_chart_div").html("<canvas id='survey_chart' height='150'></canvas>");
    var survey_id = $("#view_surveys_form #survey_id").val();
    $.ajax({
      url: "/<?=APP_NAME?>/fetch_survey_data_admin.php",
      method: "post",
      data: {survey_id: survey_id},
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

</script>
