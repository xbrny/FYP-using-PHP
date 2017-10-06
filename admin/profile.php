<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/admin/includes/header.php";
// ------------------------------------------------------------------------------------ //
if(!is_logged_in()) {
  error_login_redirect();
}

if(isset($_GET["delete"])) {
  $comment_id = sanitize($_GET["delete"]);
  deleteComment($comment_id);
  header("location: profile");
}
?>
<div class="row">

  <div class="col-sm-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;Your Details</h3>
      </div>

      <div class="panel-body">
        <table class="table table-bordered">
          <tr>
            <th width="25%">Full Name</th>
            <td><?=ucfirst($user_data["full_name"])?></td>
          </tr>
          <tr>
            <th width="25%">ID</th>
            <td><?=$user_data["login_id"]?></td>
          </tr>
          <tr>
            <th width="25%">Email</th>
            <td><?=$user_data["email"]?></td>
          </tr>
          <tr>
            <?php
            if(is_permission("lecturer") || is_permission("hod")) :
              ?>
              <th width="25%">Department</th>
              <td>
                <?php
                $department_result = getDepartment($user_data["department_id"]);
                $department = mysqli_fetch_assoc($department_result);
                echo ucfirst($department["name"]);
                ?>
              </td>
              <?php
            endif;
            if(is_permission("student")) :
              ?>
              <th width="25%">Major</th>
              <td>
                <?php
                $major_result = getMajor($user_data["major_id"]);
                $major = mysqli_fetch_assoc($major_result);
                echo strtoupper($major["name"]);
                ?>
              </td>
              <?php
            endif;
            ?>
          </tr>
          <tr>
            <th width="25%">Permission</th>
            <td><?=strtoupper($user_data["permission"])?></td>
          </tr>
        </table>
      </div> <!-- panel body -->
    </div> <!-- panel -->
  </div>

  <div class="col-sm-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-comment"></span>&nbsp;&nbsp;All Your Comments</h3>
      </div>

      <div class="panel-body">
        <?php
        $comments_result = getCommentsByUser($user_id);
        while($comments = mysqli_fetch_assoc($comments_result)) :
          $subject_result = getSubject($comments["subject_id"]);
          $subject = mysqli_fetch_assoc($subject_result);
          ?>
          <div class="panel panel-default">
            <div class="panel-body">
              <p class="text-justify comment-content">
                <?=$comments["content"]?>
              </p>
            </div> <!-- end panel body -->
            <div class="panel-footer">
              <div class="clearfix">
                <small>Date posted: <?=pretty_date($comments["posted_at"])?></small><br>
                <small>Subject: <a href="/<?=APP_NAME?>/subject.php?id=<?=$subject["id"]?>" target="_blank"><?= $subject["name"] ?></a></small>
                <?php
                if(is_logged_in() && ($comments["user_id"] == $user_id)) {
                  ?>
                  <a href="profile?delete=<?=$comments["id"]?>" class="btn btn-xs btn-danger pull-right delete-btn mr-2"><span class="glyphicon glyphicon-trash"></span></a>
                  <?php
                }
                ?>
              </div>
            </div> <!-- end panel footer -->
          </div> <!-- end panel -->

          <?php
        endwhile;
        ?>
      </div> <!-- panel body -->
    </div> <!-- panel -->
  </div>

</div> <!-- end rows -->


<?php
// ------------------------------------------------------------------------------------ //
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/footer.php";
?>

<script type="text/javascript">
$(document).ready(function() {

  $(".delete-btn").click(function() {
    if(confirm("Are you sure you want to delete this comment?") == true) {
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
