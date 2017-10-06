</div>
<hr>
<footer class="text-center">
  &copy; Copyright <?=date("Y");?> - Elective Subject Information System
  <?php if (is_logged_in()): ?>
    <br>Logged in as <?= ucfirst($user_data["full_name"]) ."-" .ucfirst($user_data["permission"]) . "-" . strtoupper($user_data["login_id"])?>
  <?php endif; ?>
</footer>
</body>
</html>
