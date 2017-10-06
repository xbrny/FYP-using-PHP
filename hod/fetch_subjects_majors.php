<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/esis/core/init.php";
ob_start();
$major_id = @$_POST["major"];
$subject_m_result = getSubjectsByMajor($major_id);
while($subject_m = mysqli_fetch_assoc($subject_m_result)) :
?>
<tr>
  <td width='80%'><?=$subject_m["code"]." - ".$subject_m["name"]?></td>
  <td width='20%' class="text-center">
    <a href="subjects_majors?remove=<?=$subject_m["id"]?>" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove-sign"></span></a>
  </td>
</tr>
<?php
endwhile;
?>
<tr>
  <td class="text-center" colspan="2">
    <button type="button" class="btn btn-success btn-block" onclick="addSubject(<?=$major_id?>);"><span class="glyphicon glyphicon-plus-sign"></span> Add Subject</button>
  </td>
</tr>


<?php
echo ob_get_clean();
?>
