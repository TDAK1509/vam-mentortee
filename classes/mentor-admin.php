<?php
if (!defined('ABSPATH')) {
  die;
}

class VamMentorAdmin {
  private $enqueueHandleName = "vammentor-admin";
  private $careerExpertiseData;

  function __construct() {
    $this->careerExpertiseData = $this->getCareerFieldExpertiseData();
  }
  
  private function getCareerFieldExpertiseData() {
    $json = file_get_contents(DIR_PLUGIN . "/json/career_field_expertise.json");
    $jsonToArray = (array) json_decode($json);
    return $jsonToArray;
  }

  public static function init() {
    $self = new self();
    // Include js css file
    add_action('show_user_profile', [$self, 'importCss']);
    add_action('show_user_profile', [$self, 'importJs']);

    // Add extra fields to user profile form
    add_action( 'show_user_profile', [$self, 'addExtraProfileFields'] );
    add_action( 'edit_user_profile', [$self, 'addExtraProfileFields'] );

    // Update extra fields on user update
    add_action('personal_options_update', [$self, 'updateExtraProfileFields']);
    add_action('edit_user_profile_update', [$self, 'updateExtraProfileFields']);

    // Add user role
    add_action('init', [$self, 'addUserRole']);

    // Add CSV to admin sidebar
    add_action('admin_menu', [$self, 'add_upload_csv_navigator']);
  }

  public function importCss() {
    wp_enqueue_style($this->enqueueHandleName, DIR_PLUGIN . "/assets/css/mentor_admin.css");
  }

  public function importJs() {
    wp_enqueue_script($this->enqueueHandleName, DIR_PLUGIN . "/assets/js/mentor-admin.js", [], false, true);
    $this->sendDataToUseInJavascriptFiles();
  }

  private function sendDataToUseInJavascriptFiles() {
    wp_localize_script( $this->enqueueHandleName, "phpCareerExpertiseObj", $this->careerExpertiseData );
  }

  public function addUserRole() {
    $capabilities = [
      'read' => true
    ];
    remove_role('mentor');
    add_role('mentor', 'Mentor', $capabilities);
  }

  public function addExtraProfileFields(WP_User $user) {
    if (!$this->userIsAdmin() && !$this->userIsMentor()) {
      return;
    }

    echo '
    <h2>Mentor profile</h2>
    <table class="form-table" role="presentation">
      <tbody>
        ' . $this->getAvatarUploadField() . '
        ' . $this->getRadioFieldHTML("Gender", "gender", $this->getGenderData()) . '
        ' . $this->getTextFieldHTML("Company", "company", $this->getFieldValueFromServer("company")) . '
        ' . $this->getTextFieldHTML("Title", "title", $this->getFieldValueFromServer("title")) . '
        ' . $this->getTextFieldHTML("Year of experience", "year_of_experience", $this->getFieldValueFromServer("year_of_experience")) . '
        ' . $this->getTextFieldHTML("Workplace location", "workplace_location", $this->getFieldValueFromServer("workplace_location")) . '
        ' . $this->getTextFieldHTML("Soft skills", "soft_skills", $this->getFieldValueFromServer("soft_skills")) . '
        ' . $this->getTextFieldHTML("Subjects for cross-mentoring", "subjects_cross_mentoring", $this->getFieldValueFromServer("subjects_cross_mentoring")) . '
        ' . $this->getSelectFieldHTML("Mentoring program", "mentoring_program") . '
        ' . $this->getSelectFieldHTML("Career field", "career_field") . '
        ' . $this->getSelectFieldHTML("Expertised", "expertise") . '
      </tbody>
    </table>';
  }

  private function getAvatarUploadField() {
    $defaultAvatar = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSQ0WPascJHnRmamqyCeLDVPaWxuVCkuHeqRw&usqp=CAU";
    $avatar = $this->getFieldValueFromServer("vam_avatar") ?: $defaultAvatar;
    return '
      <tr>
        <th><label for="vam_avatar">VAM Avatar</label></th>
        <td>
          <img src="' . $avatar . '" width="50px" height="auto" style="object-fit: cover;" />
          <input type="file" name="vam_avatar" id="vam_avatar" />
        </td>
      </tr>
    ';
  }

  private function getRadioFieldHTML($label, $name, $radios) {
    $radiosHTML = "";
    $radiosLength = count($radios);

    for($i = 0; $i < $radiosLength; $i++) {
      $radio = $radios[$i];
      $id = $radio['id'];
      $value = $radio['value'];
      $radioLabel = $radio['label'];
      $checked = $radio['checked'] ? 'checked' : '';

      if (($i + 1) < $radiosLength) {
        $radiosHTML .= "
          <label class='mentor-admin__radio-field' for='$id'>
            <input name='$name' id='$id' type='radio' value='$value' $checked />
            $radioLabel
          </label>
        ";
      } else {
        $radiosHTML .= "
          <label for='$id'>
            <input name='$name' id='$id' type='radio' value='' $checked />
            $radioLabel
            <input name='$id' type='text' value='$value' />
          </label>
        ";
      }
    }

    return '
      <tr>
        <th scope="row">' . $label . '</th>
        <td>
          ' . $radiosHTML . '
        </td>
      </tr>
    ';
  }

  private function getTextFieldHTML($label, $name, $value = '') {
    return "
    <tr class='user-url-wrap'>
      <th><label for='$name'>$label</label></th>
      <td><input type='text' name='$name' id='$name' value='$value' class='regular-text code'></td>
    </tr>";
  }

  private function getSelectFieldHTML($label, $name) {
    $optionsHTML = $this->getSelectOptionsHTMLByFieldName($name);

    return "
    <tr class='user-url-wrap'>
      <th><label>$label</label></th>
      <td>
        <select class='mentor-admin__select-field' name='$name' id='$name'>$optionsHTML</select>
      </td>
    </tr>";
  }

  private function getSelectOptionsHTMLByFieldName($name) {
    switch($name) {
      case "mentoring_program":
        return $this->getOptionsHTMLMentoringProgram();
      case "career_field":
        return $this->getOptionsHTMLCareerField();
      case "expertise":
        return $this->getOptionsHTMLExpertise();
      default:
        return [];
    }
  }

  private function getOptionsHTMLMentoringProgram() {
    $selectedValue = $this->getFieldValueFromServer("mentoring_program");
    $options = ["UEH Mentoring", "BK Mentoring", "FTU2 Mentoring", "HN Mentoring"];

    $html = "<option value=''>Click to select</option>";

    foreach ($options as $option) {
      if ($option === $selectedValue) {
        $html .= "<option selected>$option</option>";
      } else {
        $html .= "<option>$option</option>";
      }
      
    }

    return $html;
  }

  private function getOptionsHTMLCareerField() {
    $selectedValue = $this->getFieldValueFromServer("career_field");
    $options = $this->getCareerFieldValues();

    $html = "<option value=''>Click to select</option>";

    foreach ($options as $option) {
      if ($option === $selectedValue) {
        $html .= "<option selected>$option</option>";
      } else {
        $html .= "<option>$option</option>";
      }
      
    }

    return $html;
  }

  private function getCareerFieldValues() {
    $careerOptions = array_keys($this->careerExpertiseData);
    sort($careerOptions);
    return $careerOptions;
  }

  private function getOptionsHTMLExpertise() {
    $options = $this->getExpertiseFieldValues();
    $selectedExpertise = $this->getFieldValueFromServer("expertise");
    $html = "<option value=''>Please select career field first</option>";

    foreach ($options as $option) {
      if ($option === $selectedExpertise) {
        $html .= "<option selected>$option</option>";
      } else {
        $html .= "<option>$option</option>";
      }
      
    }

    return $html;
  }

  private function getExpertiseFieldValues() {
    $selectedCareer = $this->getFieldValueFromServer("career_field");

    if (!$selectedCareer) {
      return [];
    }

    $expertises = $this->careerExpertiseData[$selectedCareer];
    sort($expertises);
    return $expertises;
  }

  private function getGenderData() {
    $gender = $this->getFieldValueFromServer("gender");
    $genderList = ["Nam", "Nữ"];

    $data = [
      [
        "id" => "gender-1",
        "value" => "Nam",
        "label" => "Nam",
        "checked" => $gender === $genderList[0]
      ],

      [
        "id" => "gender-2",
        "value" => "Nữ",
        "label" => "Nữ",
        "checked" => $gender === $genderList[1]
      ],

      [
        "id" => "gender_other",
        "value" => !in_array($gender, $genderList) ? $gender : "",
        "label" => "Khác",
        "checked" => !in_array($gender, $genderList)
      ]
    ];

    return $data;
  }

  private function getFieldValueFromServer($fieldName) {
    $userId = get_current_user_id();

    if ($this->userIsAdmin()) {
      $userId = $_GET["user_id"];
    }

    return get_user_meta($userId, $fieldName, true);
  }

  private function userIsAdmin() {
    return current_user_can('administrator');
  }

  private function userIsMentor() {
    $user = wp_get_current_user();
    $roles = ( array ) $user->roles;
    return in_array("mentor", $roles);
  }

  public function updateExtraProfileFields($userId) {
    if (!current_user_can('edit_user', $userId)) {
      return;
    }

    // Avatar
    $attachment_id = media_handle_upload('vam_avatar', 0);
    $image_url = wp_get_attachment_url($attachment_id);
    update_user_meta($userId, 'vam_avatar', $image_url);

    // Radio fields
    update_user_meta($userId, 'gender', $_REQUEST['gender'] !== "" ? $_REQUEST['gender'] : $_REQUEST['gender_other']);

    // Text fields
    update_user_meta($userId, 'company', $_REQUEST['company']);
    update_user_meta($userId, 'title', $_REQUEST['title']);
    update_user_meta($userId, 'year_of_experience', $_REQUEST['year_of_experience']);
    update_user_meta($userId, 'workplace_location', $_REQUEST['workplace_location']);
    update_user_meta($userId, 'soft_skills', $_REQUEST['soft_skills']);
    update_user_meta($userId, 'subjects_cross_mentoring', $_REQUEST['subjects_cross_mentoring']);

    // Select fields
    update_user_meta($userId, 'mentoring_program', $_REQUEST['mentoring_program']);
    update_user_meta($userId, 'career_field', $_REQUEST['career_field']);
    update_user_meta($userId, 'expertise', $_REQUEST['expertise']);
  }

  public function add_upload_csv_navigator() {
    add_menu_page(
      "VamMentorAdmin",
      "Tạo Mentors bằng CSV",
      "manage_options",
      "upload_mentor_csv",
      [$this, "get_upload_csv_html"],
      "dashicons-upload",
      110,
    );
  }

  public function get_upload_csv_html() {
    require_once plugin_dir_path(__FILE__) . '../templates/csv_navigator_button.php';
  }
}