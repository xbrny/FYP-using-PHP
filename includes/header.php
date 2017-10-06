<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=ucfirst(APP_NAME)?></title>
  <link href="/<?=APP_NAME?>/assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="/<?=APP_NAME?>/assets/css/custom.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="/<?=APP_NAME?>/assets/datatable/datatables.min.css"/>
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
  <script src="/<?=APP_NAME?>/assets/jquery/jquery.min.js"></script>
  <script type="text/javascript" src="/<?=APP_NAME?>/assets/datatable/datatables.min.js"></script>
  <script src="/<?=APP_NAME?>/assets/js/Chart.min.js"></script>
  <script src="/<?=APP_NAME?>/assets/js/bootstrap.min.js"></script>
  <script src="/<?=APP_NAME?>/assets/js/readmore.min.js"></script>
  <script src="/<?=APP_NAME?>/assets/ckeditor/ckeditor.js"></script>
</head>
<body>
  <nav class="navbar navbar-default navbar-static-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/<?=APP_NAME?><?=((is_logged_in())? "/".$_SESSION["permission"] : "")?>/index">
          <?=strtoupper(APP_NAME)?>
        </a>
      </div>
      <div id="navbar" class="collapse navbar-collapse text-center">
        <ul class="nav navbar-nav">
          <?php if(is_logged_in()) : ?>
            <?php if(is_permission("student")) : ?>
              <li><a href="/<?=APP_NAME?>/student/index"><span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;Home</a></li>
              <li><a href="/<?=APP_NAME?>/subjects"><span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;Subjects</a></li>
              <li><a href="/<?=APP_NAME?>/student/surveys"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Survey</a></li>
              <!-- <li><a href="#"><span class="glyphicon glyphicon-tasks"></span>&nbsp;&nbsp;Vote</a></li> -->
            <?php endif; ?>
            <?php if(is_permission("lecturer")) : ?>
              <li><a href="/<?=APP_NAME?>/lecturer/index"><span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;Home</a></li>
              <?php
              $id = $_SESSION["user_id"];
              $sql = "SELECT subjects.id AS id, subjects.name AS name FROM users INNER JOIN subjects ON users.id = subjects.user_id WHERE users.id = $id";
              $subjects_result = $db->query($sql) or die;
              $subjects_count = mysqli_num_rows($subjects_result);
              if($subjects_count > 0) { ?>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;Subjects <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                    <?php
                    while($subjects = mysqli_fetch_assoc($subjects_result)) :
                      ?>
                      <li><a href="/<?=APP_NAME?>/subject?id=<?=$subjects["id"]?>"><?=$subjects["name"]?></a></li>
                      <?php
                    endwhile;
                    ?>
                  </ul>
                </li>
                <?php
              } else { ?>
                <li><a href="/<?=APP_NAME?>/subjects"><span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;Subjects</a></li>
                <?php
              }
              ?>
            <?php endif; ?>
            <?php if(is_permission("hod")) : ?>
              <li><a href="/<?=APP_NAME?>/hod/index"><span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;Home</a></li>
              <?php
              $id = $_SESSION["user_id"];
              $sql = "SELECT subjects.id AS id, subjects.name AS name FROM users INNER JOIN subjects ON users.id = subjects.user_id WHERE users.id = $id";
              $subjects_result = $db->query($sql) or die;
              $subjects_count = mysqli_num_rows($subjects_result);
              if($subjects_count > 0) { ?>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;Subjects <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                    <?php
                    while($subjects = mysqli_fetch_assoc($subjects_result)) :
                      ?>
                      <li><a href="/<?=APP_NAME?>/hod/subjects">Manage Subjects</a></li>
                      <li><a href="/<?=APP_NAME?>/subject?id=<?=$subjects["id"]?>"><?=$subjects["name"]?></a></li>
                      <?php
                    endwhile;
                    ?>
                  </ul>
                </li>
                <?php
              } else { ?>
                <li><a href="/<?=APP_NAME?>/hod/subjects"><span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;Subjects</a></li>
                <?php
              }
              ?>
              <li><a href="/<?=APP_NAME?>/hod/surveys"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Survey</a></li>
              <li><a href="/<?=APP_NAME?>/hod/subjects_majors"><span class="glyphicon glyphicon-th-list"></span>&nbsp;&nbsp;Majors</a></li>
              <li><a href="/<?=APP_NAME?>/hod/comments"><span class="glyphicon glyphicon-folder-close"></span>&nbsp;&nbsp;Archive</a></li>
              <!-- <li><a href="#"><span class="glyphicon glyphicon-tasks"></span>&nbsp;&nbsp;Vote</a></li> -->
            <?php endif; ?>
          <?php endif; ?>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <?php if(!is_logged_in()) : ?>
            <li><a href="subjects">Subject</a></li>
            <li><a href="about">About</a></li>
            <li>
              <p class="navbar-btn"><a href="index" class="btn btn-default">Login</a></p>
            </li>
          <?php endif; ?>
          <?php if(is_logged_in()) : ?>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="/<?=APP_NAME?>/shared/profile"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;User Profile</a></li>
                <li><a href="/<?=APP_NAME?>/shared/settings"><span class="glyphicon glyphicon-cog"></span>&nbsp;&nbsp;Settings</a></li>
                <li class="divider"></li>
                <li><a href="/<?=APP_NAME?>/logout"><span class="glyphicon glyphicon-log-out"></span>&nbsp;&nbsp;Logout</a></li>
              </ul>
            </li>
          <?php endif; ?>
        </ul>
      </div><!--/.nav-collapse -->
    </div>
  </nav>
  <div class="container">
    <?php
    if(isset($_SESSION["user_id"])) {
      $user_id = $_SESSION["user_id"];
      $user_data_result = getUser($user_id);
      $user_data = mysqli_fetch_assoc($user_data_result);
    }

    if(isset($_SESSION["success_flash"])) {
      echo '<div class="alert alert-success alert-dismissible" role="alert">';
      echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
      echo "<strong>" . $_SESSION["success_flash"]. "</strong>";
      echo '</div>';
      unset($_SESSION["success_flash"]);
    }

    if(isset($_SESSION["error_flash"])) {
      echo '<div class="alert alert-danger alert-dismissible" role="alert">';
      echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
      echo "<strong>" . $_SESSION["error_flash"]. "</strong>";
      echo '</div>';
      unset($_SESSION["error_flash"]);
    }
    ?>
