<?php
// --------------------comments----------------------
function getComments() {
  global $db;
  $sql = "SELECT comments.id, comments.content, comments.posted_at, comments.subject_id, subjects.name as subject_name
          FROM comments JOIN subjects ON comments.subject_id=subjects.id
          WHERE comments.archived=0  ORDER BY posted_at DESC";
  $comments_result = $db->query($sql) or die("Select comments failed: " . mysqli_error($db));
  return $comments_result;
}
function getCommentsByDepartment($department_id) {
  global $db;
  $sql = "SELECT comments.id, comments.content, comments.posted_at, comments.subject_id, subjects.name as subject_name
          FROM comments JOIN subjects ON comments.subject_id=subjects.id
          WHERE subjects.department_id=$department_id AND comments.archived=0  ORDER BY posted_at DESC";
  $comments_result = $db->query($sql) or die("Select comments failed: " . mysqli_error($db));
  return $comments_result;
}
function getCommentsByUser($user_id) {
  global $db;
  $sql = "SELECT * FROM comments WHERE user_id=$user_id AND archived=0";
  $comment_result = $db->query($sql) or die("Select comment failed: " . mysqli_error($db));
  return $comment_result;
}
function getCommentsBySubject($comment_id) {
  global $db;
  $sql = "SELECT * FROM comments WHERE subject_id=$comment_id AND archived=0 ORDER BY posted_at DESC";
  $comment_result = $db->query($sql) or die("Select comment failed: " . mysqli_error($db));
  return $comment_result;
}
function getArchivedComments($department_id) {
  global $db;
  $sql = "SELECT comments.id, comments.content, comments.posted_at, comments.subject_id
          FROM comments JOIN subjects ON comments.subject_id=subjects.id
          WHERE subjects.department_id=$department_id AND comments.archived=1  ORDER BY posted_at DESC LIMIT 5";
  $comment_result = $db->query($sql) or die("Select comment failed: " . mysqli_error($db));
  return $comment_result;
}
function getAllArchivedComments($department_id) {
  global $db;
  $sql = "SELECT comments.id, comments.content, comments.posted_at, comments.subject_id
          FROM comments JOIN subjects ON comments.subject_id=subjects.id
          WHERE subjects.department_id=$department_id AND comments.archived=1  ORDER BY posted_at DESC";
  $comment_result = $db->query($sql) or die("Select comment failed: " . mysqli_error($db));
  return $comment_result;
}
function getCommentCount($department_id) {
  global $db;
  $sql = "SELECT COUNT(*) AS total
          FROM comments JOIN subjects ON comments.subject_id=subjects.id
          WHERE subjects.department_id=$department_id";
  $comment_result = $db->query($sql) or die("Select comment failed: " . mysqli_error($db));
  return $comment_result;
}
function getArchivedCommentCount($department_id) {
  global $db;
  $sql = "SELECT COUNT(*) AS total
          FROM comments JOIN subjects ON comments.subject_id=subjects.id
          WHERE subjects.department_id=$department_id AND comments.archived=1";
  $comment_result = $db->query($sql) or die("Select comment failed: " . mysqli_error($db));
  return $comment_result;
}
function restoreComment($id) {
  global $db;
  $sql = "UPDATE comments SET archived=0 WHERE id=$id";
  $db->query($sql) or die("Delete comment failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully restore comment";
}
function permanentDeleteComment($id) {
  global $db;
  $sql = "DELETE FROM comments WHERE id=$id";
  $db->query($sql) or die("Delete comment failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully delete comment";
}
function deleteComment($id) {
  global $db;
  $sql = "UPDATE comments SET archived=1 WHERE id=$id";
  $db->query($sql) or die("Delete comment failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully delete comment";
}
function createComment($content, $posted_at, $user_id, $subject_id) {
  global $db;
  $sql = "INSERT INTO comments(content, posted_at, user_id, subject_id) VALUES('$content', '$posted_at', '$user_id', '$subject_id')";
  $db->query($sql) or die("Insert comment failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully post comment";
}
?>
