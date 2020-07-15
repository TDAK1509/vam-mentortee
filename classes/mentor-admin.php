<?php
if (!defined('ABSPATH')) {
  die;
}

class VamMentorAdmin {
  public static function init() {
    $self = new self();
    // Include css file
    add_action('show_user_profile', [$self, 'importCss']);

    // Add extra fields to user profile form
    add_action( 'show_user_profile', [$self, 'addExtraProfileFields'] );

    // Update extra fields on user update
    add_action('personal_options_update', [$self, 'updateExtraProfileFields']);

    // Add user role
    add_action('init', [$self, 'addUserRole']);    
  }

  public function importCss() {
    wp_enqueue_style('vammentor-admin', DIR_PLUGIN . "/css/mentor_admin.css");
  }

  public function addUserRole() {
    $capabilities = [
      'read' => true
    ];
    remove_role('mentor');
    add_role('mentor', 'Mentor', $capabilities);
  }

  public function addExtraProfileFields(WP_User $user) {
    if (!$this->userIsMentor()) {
      return;
    }

    echo '
    <h2>Mentor profile</h2>
    <table class="form-table" role="presentation">
      <tbody>
        ' . $this->getRadioFieldHTML("Gender", "gender", $this->getGenderData()) . '
        ' . $this->getTextFieldHTML("Company", "company", $this->getFieldValueFromServer("company")) . '
        ' . $this->getTextFieldHTML("Title", "title", $this->getFieldValueFromServer("title")) . '
        ' . $this->getTextFieldHTML("Phone", "phone", $this->getFieldValueFromServer("phone")) . '
        ' . $this->getTextFieldHTML("Method of contact", "method_of_contact", $this->getFieldValueFromServer("method_of_contact")) . '
        ' . $this->getTextFieldHTML("Meeting frequency", "meeting_frequency", $this->getFieldValueFromServer("meeting_frequency")) . '
        ' . $this->getTextFieldHTML("Year of experience", "year_of_experience", $this->getFieldValueFromServer("year_of_experience")) . '
        ' . $this->getRadioFieldHTML("Degree", "degree", $this->getDegreeData()) . '
        ' . $this->getTextFieldHTML("Specific topics you feel comfortable advising about", "topics", $this->getFieldValueFromServer("topics")) . '
        ' . $this->getTextFieldHTML("Describe the activities, interests, and/or hobbies that are most meaningful to you", "hobbies", $this->getFieldValueFromServer("hobbies")) . '
        ' . $this->getTextFieldHTML("Mentee capacity", "mentee_capacity", $this->getFieldValueFromServer("mentee_capacity")) . '
        ' . $this->getSelectFieldHTML("Mentoring program", "mentoring_program", $this->getFieldValueFromServer("mentoring_program")) . '
      </tbody>
    </table>';
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
        <select class='mentor-admin__select-field' name='$name'>$optionsHTML</select>
      </td>
    </tr>";
  }

  private function getSelectOptionsHTMLByFieldName($name) {
    switch($name) {
      case "mentoring_program":
        return $this->getOptionsHTMLMentoringProgram();
      default:
        return [];
    }
  }

  private function getOptionsHTMLMentoringProgram() {
    $selectedValue = $this->getFieldValueFromServer("mentoring_program");
    $options = ["UEH Mentoring", "BK Mentoring", "FTU2 Mentoring", "HN Mentoring"];

    $html = "<option>Click to select</option>";

    foreach ($options as $option) {
      if ($option === $selectedValue) {
        $html .= "<option selected>$option</option>";
      } else {
        $html .= "<option>$option</option>";
      }
      
    }

    return $html;
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

  private function getDegreeData() {
    $degree = $this->getFieldValueFromServer("degree");
    $degreeList = ["Cao đẳng", "Đại học", "Thạc sĩ", "Tiến sĩ"];

    $data = [
      [
        "id" => "degree-1",
        "value" => "Cao đẳng",
        "label" => "Cao đẳng",
        "checked" => $degree === $degreeList[0]
      ],

      [
        "id" => "degree-2",
        "value" => "Đại học",
        "label" => "Đại học",
        "checked" => $degree === $degreeList[1]
      ],

      [
        "id" => "degree-3",
        "value" => "Thạc sĩ",
        "label" => "Thạc sĩ",
        "checked" => $degree === $degreeList[2]
      ],

      [
        "id" => "degree-4",
        "value" => "Tiến sĩ",
        "label" => "Tiến sĩ",
        "checked" => $degree === $degreeList[3]
      ],

      [
        "id" => "degree_other",
        "value" => $degree,
        "label" => "Khác",
        "checked" => !in_array($degree, $degreeList)
      ],
    ];

    return $data; 
  }

  private function getFieldValueFromServer($fieldName) {
    return get_user_meta(get_current_user_id(), $fieldName, true);
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

    // Radio fields
    update_user_meta($userId, 'gender', $_REQUEST['gender'] !== "" ? $_REQUEST['gender'] : $_REQUEST['gender_other']);
    update_user_meta($userId, 'degree', $_REQUEST['degree'] !== "" ? $_REQUEST['degree'] : $_REQUEST['degree_other']);

    // Text fields
    update_user_meta($userId, 'company', $_REQUEST['company']);
    update_user_meta($userId, 'title', $_REQUEST['title']);
    update_user_meta($userId, 'phone', $_REQUEST['phone']);
    update_user_meta($userId, 'topics', $_REQUEST['topics']);
    update_user_meta($userId, 'method_of_contact', $_REQUEST['method_of_contact']);
    update_user_meta($userId, 'year_of_experience', $_REQUEST['year_of_experience']);
    update_user_meta($userId, 'topics', $_REQUEST['topics']);
    update_user_meta($userId, 'hobbies', $_REQUEST['hobbies']);
    update_user_meta($userId, 'mentee_capacity', $_REQUEST['mentee_capacity']);

    // Select fields
    update_user_meta($userId, 'mentoring_program', $_REQUEST['mentoring_program']);
  }
}