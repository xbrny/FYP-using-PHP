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
  <script src="http://netsh.pp.ua/upwork-demo/1/js/typeahead.js"></script>
</head>
<body>
  <?php if(is_logged_in()) : ?>
    <nav class="navbar navbar-default">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index"><?=strtoupper(APP_NAME)?></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="index"><span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;Home</a></li>
            <li><a href="users"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;User</a></li>
            <li><a href="departments"><span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;Department</a></li>
            <li><a href="surveys"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Survey</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="profile"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;User Profile</a></li>
                <li><a href="settings"><span class="glyphicon glyphicon-cog"></span>&nbsp;&nbsp;Settings</a></li>
                <li class="divider"></li>
                <li><a href="/<?=APP_NAME?>/logout"><span class="glyphicon glyphicon-log-out"></span>&nbsp;&nbsp;Logout</a></li>
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
  <?php endif; ?>
  <div class="container">
    <?php
    if(isset($_SESSION["user_id"])) {
      $user_id = $_SESSION["user_id"];
      $user_data_result = getUser($user_id);
      $user_data = mysqli_fetch_assoc($user_data_result);
    }

    if(isset($_SESSION["success_flash"])) {
      echo '<div class="flash-box">';
      echo '<div class="alert alert-success alert-dismissible" role="alert">';
      echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
      echo "<strong>" . $_SESSION["success_flash"]. "</strong>";
      echo '</div>';
      echo '</div>';
      unset($_SESSION["success_flash"]);
    }

    if(isset($_SESSION["error_flash"])) {
      echo '<div class="flash-box">';
      echo '<div class="alert alert-danger alert-dismissible" role="alert">';
      echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
      echo "<strong>" . $_SESSION["error_flash"]. "</strong>";
      echo '</div>';
      echo '</div>';
      unset($_SESSION["error_flash"]);
    }
    ?>
