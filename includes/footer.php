</div><!-- /.container -->
<hr>
<footer class="text-center">
  &copy; Copyright <?=date("Y");?> - Elective Subject Information System
  <?php if (is_logged_in()): ?>
    <br>Logged in as <?= ucfirst($user_data["full_name"]) ."-" .ucfirst($user_data["permission"]) . "-" . strtoupper($user_data["login_id"])?>
    <?php if (is_permission("student")):
      $major_result = getMajor($user_data["major_id"]);
      $major = mysqli_fetch_assoc($major_result);
      ?>
      <br><?= ucfirst($major["name"]) ?>
    <?php endif; ?>
  <?php endif; ?>
</footer>
</body>
</html>
