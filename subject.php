<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/header.php";
// ------------------------------------------------------------------------------------ //
if(!(isset($_GET["id"]) || isset($_GET["edit"]))) {
  header("location: subjects.php");
}

if(isset($_GET["id"])) {
  $subject_id = $_GET["id"];
}

if(isset($_GET["edit"])) {
  $subject_id = $_GET["edit"];
}

if(@$_POST["create_comment"]) {
  $errors = array();
  $content = sanitize($_POST["content"]);
  $user_id = sanitize($_POST["user_id"]);
  $subject_id = sanitize($_POST["subject_id"]);
  $posted_at = date("Y-m-d H:i:s");

  if(empty($content)) {
    $errors[] = "Failed to post comments, cannot leave field empty";
    display_errors($errors);
  } else {
    createComment($content, $posted_at, $user_id, $subject_id);
    header("location: subject?id=$subject_id");
  }
}

if(isset($_GET["delete"])) {
  $id = sanitize($_GET["delete"]);
  $subject_id = sanitize($_GET["subject"]);
  deleteComment($id);
  header("location: subject?id=$subject_id");
}

$subject_result = getSubject($subject_id);
$subject = mysqli_fetch_assoc($subject_result);

if(isset($_POST["update_button"])) {
  $id = sanitize($_POST["id"]);
  $description = sanitize($_POST["description"]);
  $prerequisites = sanitize($_POST["prerequisites"]);
  $assessment = sanitize($_POST["assessment"]);
  $reference = sanitize($_POST["reference"]);
  updateSubjectForLec($id, $description, $prerequisites, $assessment, $reference);
  header("location: subject?id=$subject_id");
}

?>
<div class="row">
  <div class="col-sm-8 col-sm-offset-2">
    <div class="col-sm-1 row mr-2">
      <a href="subjects" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-circle-arrow-left"></span></a>&nbsp;
    </div>
    <div class="col-sm-7 row">
      <legend>
        <?=$subject["code"]?> - <?=$subject["name"]?>
      </legend>
    </div>
  </div>

  <div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span class="">
          <span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;Subject Detail
          <?php
          if(is_logged_in() && ($subject["user_id"] == $user_id)) {
            ?>
            <a href="subject?edit=<?=$subject_id?>" class="btn btn-custom btn-xs pull-right"><span class="glyphicon glyphicon-edit"></span> Edit</a>&nbsp;
            <?php
          }
          ?>
        </span>
      </div>
      <div class="panel-body">
        <form action="subject?edit=<?=$subject["id"]?>" method="post">
          <table class="table table-bordered">
            <tr>
              <td colspan="2">
                <strong>Summary</strong>
                <p class="text-justify mt-2">
                  <?php
                  if(isset($_GET["edit"]) && (is_logged_in() && $subject["user_id"] == $user_id)) { ?>
                    <input type="hidden" name="id" value="<?=$subject["id"];?>">
                    <textarea name="description" id="description" class="form-control text-justify" rows="10" cols="80"><?=html_entity_decode($subject["description"],ENT_QUOTES, "UTF-8")?></textarea>
                    <script type="text/javascript">
                    CKEDITOR.replace("description", {
                      height: 280
                    });
                    </script>
                  <?php } else {
                    echo html_entity_decode($subject["description"], ENT_QUOTES, "UTF-8");
                  }
                  ?>
                </p>
              </td>
            </tr>
            <tr>
              <th width="25%">Prerequisites</th>
              <td>
                <?php
                if(isset($_GET["edit"]) && (is_logged_in() && $subject["user_id"] == $user_id)) { ?>
                  <input type="text" name="prerequisites" class="form-control" value="<?=$subject["prerequisites"];?>">
                <?php } else {
                  echo $subject["prerequisites"];
                }
                ?>
              </td>
            </tr>
            <tr>
              <th width="25%">Assessment</th>
              <td>
                <?php
                if(isset($_GET["edit"]) && (is_logged_in() && $subject["user_id"] == $user_id)) { ?>
                  <textarea name="assessment" class="form-control text-justify" rows="3" cols="80"><?=$subject["assessment"];?></textarea>
                <?php } else {
                  echo $subject["assessment"];
                }
                ?>
              </td>
            </tr>
            <tr>
              <th width="25%">Teaching lecturer</th>
              <td>
                <?php
                $teaching_lecturer_result = getTeachingLecturer($subject["id"]);
                $teaching_lecturer = mysqli_fetch_assoc($teaching_lecturer_result);
                echo $teaching_lecturer["full_name"];
                ?>
              </td>
            </tr>
            <tr>
              <th width="25%">References</th>
              <td>
                <?php
                if(isset($_GET["edit"]) && (is_logged_in() && $subject["user_id"] == $user_id)) { ?>
                  <textarea name="reference" class="form-control text-justify" rows="4" cols="80"><?=$subject["reference"];?></textarea>
                <?php } else {
                  echo $subject["reference"];
                }
                ?>
              </td>
            </tr>
            <?php
            if(isset($_GET["edit"]) && (is_logged_in() && $subject["user_id"] == $user_id)) { ?>
              <td colspan="2" class="text-right">
                <a href="subject?id=<?=$subject_id?>"  class="btn btn-default ">Cancel</a>
                <input type="submit" name="update_button" value="Update" class="btn btn-success">
              </td>
              <?php
            }
            ?>
          </table>
        </form>
      </div> <!-- end panel-body -->
    </div> <!-- end panel -->

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-comment  "></span>&nbsp;&nbsp;Comments</h3>
      </div>
      <div class="panel-body">
        <?php
        if(is_logged_in()) :
          ?>
          <div>
            <form method="post">
              <div class="form-group clearfix">
                <input type="hidden" name="user_id" value="<?=$user_id?>">
                <input type="hidden" name="subject_id" value="<?=$subject_id?>">
                <textarea name="content" class="form-control" rows="5" cols="80" placeholder="Write your comment for this subject"></textarea>
                <input type="submit" class="btn btn-success mt-2 pull-right" name="create_comment" value="Post">
              </div>
              <hr>
            </form>
          </div>
          <?php
        endif;
        ?>
        <?php
        $comments_result = getCommentsBySubject($subject_id);
        while($comments = mysqli_fetch_assoc($comments_result)) :
          ?>
          <div class="panel panel-default">
            <div class="panel-body">
              <p class="text-justify comment-content">
                <?=$comments["content"]?>
              </p>
            </div> <!-- end panel body -->
            <div class="panel-footer">
              <div class="text-right">
                <small>
                  Posted by:
                  <?php
                  $user_result = getUser($comments["user_id"]);
                  $poster = mysqli_fetch_assoc($user_result);
                  echo $poster["full_name"];
                  ?>
                </small> |
                <small>Posted on: <?=pretty_date($comments["posted_at"])?></small>
                <?php
                if(is_logged_in() && ($comments["user_id"] == $user_id || $subject["user_id"] == $user_id)) {
                  ?>
                  <a href="subject?subject=<?=$subject_id?>&amp;delete=<?=$comments["id"]?>" class="btn btn-xs btn-danger delete-comment ml-5"><span class="glyphicon glyphicon-trash"></span></a>
                  <?php
                }
                ?>
              </div>
            </div> <!-- end panel footer -->
          </div> <!-- end panel -->
          <?php
        endwhile;
        ?>
      </div> <!-- end panel-body -->
    </div> <!-- end panel -->
  </div> <!-- end col-sm-8 -->

  <!-- <div class="col-sm-4">
  <div class="panel panel-default">
  <div class="panel-heading">
  <h3 class="panel-title"><span class="glyphicon glyphicon-briefcase"></span>&nbsp;&nbsp;Jobs</h3>
</div>
<div class="panel-body">

</div>
</div>
</div> -->
</div> <!-- end row -->

<?php
// ------------------------------------------------------------------------------------ //
require_once $_SERVER["DOCUMENT_ROOT"] . "/".APP_NAME."/includes/footer.php";
?>

<script>

$(document).ready(function() {


  $(".delete-comment").click(function() {
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
