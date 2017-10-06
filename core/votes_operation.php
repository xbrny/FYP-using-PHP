<?php
// --------------------votes----------------------
function getVotes() {
  global $db;
  $sql = "SELECT * FROM votes";
  $votes_result = $db->query($sql) or die("Select votes failed: " . mysqli_error($db));
  return $votes_result;
}
function getVote($id) {
  global $db;
  $sql = "SELECT * FROM votes WHERE id=$id";
  $vote_result = $db->query($sql) or die("Select vote failed: " . mysqli_error($db));
  return $vote_result;
}
function getTopVotedSubject($survey_id, $department_id) {
  global $db;
  $sql = "SELECT votes.vote, subjects.name, subjects.id
          FROM votes JOIN subjects ON votes.subject_id=subjects.id
          WHERE votes.survey_id=$survey_id && subjects.department_id=$department_id
          ORDER BY votes.vote DESC LIMIT 3";
  $vote_result = $db->query($sql) or die("Select vote failed: " . mysqli_error($db));
  return $vote_result;
}
function getVotesByDepartmentJSON($survey_id, $department_id) {
  global $db;
  $sql = "SELECT s.name AS 'subject_name', v.vote FROM subjects s INNER JOIN votes v ON s.id=v.subject_Id WHERE v.survey_id=$survey_id && s.department_id=$department_id";
  $votes_data_result = $db->query($sql) or die("Error select vote data: " . mysqli_error($db));
  $votes_array= array();
  while($votes_data = mysqli_fetch_assoc($votes_data_result)) {
    $surveys_array[] = $votes_data;
  }
  $votes_json = json_encode($surveys_array, JSON_PRETTY_PRINT);
  return $votes_json;
}
function getVotesJSON($survey_id) {
  global $db;
  $sql = "SELECT s.name AS 'subject_name', v.vote FROM subjects s INNER JOIN votes v ON s.id=v.subject_Id WHERE v.survey_id=$survey_id";
  $votes_data_result = $db->query($sql) or die("Error select vote data: " . mysqli_error($db));
  $votes_array= array();
  while($votes_data = mysqli_fetch_assoc($votes_data_result)) {
    $surveys_array[] = $votes_data;
  }
  $votes_json = json_encode($surveys_array, JSON_PRETTY_PRINT);
  return $votes_json;
}
function initializeVotes($survey_id) {
  global $db;
  $subjects_result = getSubjects();
  while ($subjects = mysqli_fetch_assoc($subjects_result)) {
    $subject_id = $subjects["id"];
    $vote = 0;
    $sql = "INSERT INTO votes(survey_id, subject_id, vote) VALUES('$survey_id', '$subject_id', '$vote')";
    $db->query($sql) or die("Intialize votes failed: " . mysqli_error($db));
  }
}
function updateVote($id, $survey_id, $subject_id, $vote) {
  global $db;
  $sql = "UPDATE votes SET survey_id='$survey_id', subject_id='$subject_id', vote='$vote' WHERE id=$id";
  $db->query($sql) or die("Update vote failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully update vote";
}
function insertVote($survey_id, $subject_id) {
  global $db;
  $vote = 1;
  $sql = "SELECT * FROM votes WHERE survey_id=$survey_id && subject_id=$subject_id";
  $existing_votes_result = $db->query($sql) or die("Select vote by survey id and subject id failed: " . mysqli_error($db));
  $existing_votes_count = mysqli_num_rows($existing_votes_result);
  if($existing_votes_count == 0) {
    $sql = "INSERT INTO votes(survey_id, subject_id, vote) VALUES('$survey_id', '$subject_id', '$vote')";
    $db->query($sql) or die("Insert vote failed: " . mysqli_error($db));
  } else {
    $existing_votes = mysqli_fetch_assoc($existing_votes_result);
    $updated_vote = $existing_votes["vote"] + $vote;
    $existing_votes_id = $existing_votes["id"];
    $sql = "UPDATE votes SET vote=$updated_vote WHERE id=$existing_votes_id";
    $db->query($sql) or die("Insert vote failed: " . mysqli_error($db));
  }
  $_SESSION["success_flash"] = "Your vote has been sent, thank your for voting";
}
function deleteVotesBySurvey($survey_id) {
  global $db;
  $sql = "DELETE FROM votes WHERE survey_id=$survey_id";
  $db->query($sql) or die("Delete vote by survey failed: " . mysqli_error($db));
}
function deleteVote($id) {
  global $db;
  $sql = "DELETE FROM votes WHERE id=$id";
  $db->query($sql) or die("Delete vote failed: " . mysqli_error($db));
  $_SESSION["success_flash"] = "Successfully delete vote";
}

?>
