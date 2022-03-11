<h1>TẠO MENTOR BẰNG FILE CSV</h1>

<form method="POST" action="<?php echo admin_url( 'admin.php' ) . '?page=upload_mentor_csv'; ?>" enctype="multipart/form-data">
  <input type="file" name="mentor_csv" />
  <button class="mentor-csv__button">Tải</button>
</form>

<?php
if (isset($_FILES)) {
  $tmpName = $_FILES['mentor_csv']['tmp_name'];

  if ($tmpName == "") return;

  $csvAsArray = array_map('str_getcsv', file($tmpName));
  var_dump($csvAsArray);
}
?>