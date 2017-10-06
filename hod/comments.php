<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/header.php";
if(!is_logged_in()) {
  error_login_redirect();
}
elseif(!(is_permission("hod"))) {
  error_permission_redirect();
}


if(isset($_GET["restore"])) {
  $id = sanitize($_GET["restore"]);
  restoreComment($id);
  header("location: comments");
}

if(isset($_GET["delete"])) {
  $id = sanitize($_GET["delete"]);
  permanentDeleteComment($id);
  header("location: comments");
}

?>

<div class="row">
  <div class="col-sm-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-comment"></span>&nbsp;&nbsp;Archived comments</h3>
      </div>
      <div class="panel-body">
        <?php
        $comments_result = getAllArchivedComments($_SESSION["department_id"]);
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
                <a href="comments?restore=<?=$comments["id"]?>" class="btn btn-xs btn-success pull-right restore-btn"><span class="glyphicon glyphicon-repeat"></span> Restore</a>
                <a href="comments?delete=<?=$comments["id"]?>" class="btn btn-xs btn-danger pull-right delete-btn mr-2"><span class="glyphicon glyphicon-trash"></span> Remove</a>
              </div>
            </div> <!-- end panel footer -->
          </div> <!-- end panel -->
          <?php
        endwhile;
        ?>
      </div> <!-- end panel-body -->
    </div> <!-- end panel -->
  </div> <!-- end col-sm-8 -->


  <div class="col-sm-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;Comment Statistic</h3>
      </div>
      <div class="panel-body">
        <table class="table table-striped table-bordered">
          <tr>
            <td width="80%" class="text-grey"><h4>Comments on <?=$_SESSION["department_name"]?> subject</h4></td>
            <td class="text-lg text-grey text-center">
              <?php
              $comment_count_result = getCommentCount($_SESSION["department_id"]);
              $comment_count = mysqli_fetch_assoc($comment_count_result);
              echo $comment_count["total"];
              ?>
            </td>
          </tr>
          <tr>
            <td width="80%" class="text-grey"><h4>Archived comments on <?=$_SESSION["department_name"]?> subject</h4></td>
            <td class="text-lg text-grey text-center">
              <?php
              $comment_count_result = getArchivedCommentCount($_SESSION["department_id"]);
              $comment_count = mysqli_fetch_assoc($comment_count_result);
              echo $comment_count["total"];
              ?>
            </td>
          </tr>
        </table>
      </div> <!-- end panel-body -->
    </div> <!-- end panel -->
  </div> <!-- end col-sm-6 -->
</div> <!-- row -->

<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/footer.php";
?>


<script>
$(document).ready(function() {
  $(".restore-btn").click(function() {
    if(confirm("Are you sure you want to restore back this comment?") == true) {
      return true;
    } else {
      return false;
    }
  });

  $(".delete-btn").click(function() {
    if(confirm("Are you sure you want to permanently delete this comment?") == true) {
      return true;
    } else {
      return false;
    }
  });


  $(".comment-content").readmore({
    lessLink: '<a href="#">Read less</a>',
  });

});
</script>
