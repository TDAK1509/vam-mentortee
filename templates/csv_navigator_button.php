<h1>TẠO MENTOR BẰNG FILE CSV</h1>

<a href="<?php echo DIR_PLUGIN . "/assets/csv/sample_vam.csv"; ?>" download>
  Tải file CSV mẫu
</a>

<form method="POST" action="<?php echo admin_url( 'admin.php' ) . '?page=upload_mentor_csv'; ?>" enctype="multipart/form-data">
  <p><input type="file" name="mentor_csv" /></p>
  <p><input type="submit" class="button" value="Tạo Users"></p>
</form>

<?php
if (isset($_FILES)) {
  $tmpName = $_FILES['mentor_csv']['tmp_name'];

  if ($tmpName == "") return;

  $csvAsArray = array_map('str_getcsv', file($tmpName));

  try {
    for($i = 1; $i < count($csvAsArray); $i++) {
      $row = &$csvAsArray[$i];
      $isCreated = createUser($row);
      if (!$isCreated) {
        echo "Bỏ qua username <strong>$row[0]</strong> vì nó đã tồn tại.<br>";
      }
    }
    echo "Tạo users hoàn tất.";
  } catch (Exception $e) {
      echo 'Error creating user: ',  $e->getMessage(), "\n";
  }
}

function createUser($userInfo) {
  $username = $userInfo[0];
	$password = $userInfo[1];
	$email= $userInfo[2];
	$first_name = $userInfo[3];
	$last_name = $userInfo[4];
	$gender = $userInfo[5];
	$company = $userInfo[6];
	$title = $userInfo[7];
	$phone = $userInfo[8];
	$method_of_contact = $userInfo[9];
	$meeting_frequency = $userInfo[10];
	$year_of_experience = $userInfo[11];
	$degree = $userInfo[12];
	$topics = $userInfo[13];
	$hobbies = $userInfo[14];
	$mentee_capacity = $userInfo[15];
	$mentoring_program = $userInfo[16];
	$career_field = $userInfo[17];
	$expertise = $userInfo[18];

  $meta_input = array(
    "first_name" => $first_name,
    "last_name" => $last_name,
    "gender" => $gender,
    "company" => $company,
    "title" => $title,
    "phone" => $phone,
    "method_of_contact" => $method_of_contact,
    "meeting_frequency" => $meeting_frequency,
    "year_of_experience" => $year_of_experience,
    "degree" => $degree,
    "topics" => $topics,
    "hobbies" => $hobbies,
    "mentee_capacity" => $mentee_capacity,
    "mentoring_program" => $mentoring_program,
    "career_field" => $career_field,
    "expertise" => $expertise,
  );

	if (username_exists($username)) {
    return false;
  }

  $userData = array(
    "user_pass" => $password,
    "user_login" => $username,
    "user_email" => $email,
    "first_name" => $first_name,
    "last_name" => $last_name,
    "role" => "mentor",
    "meta_input" => $meta_input,
  );
  
  $userId = wp_create_user( $username, $password, $email);
  $user = new WP_User( $userId );
  $user->set_role( 'mentor' );

  foreach ( $meta_input as $key => $value ) {
    update_user_meta($userId, $key, $value);
  }

  return true;
}
?>