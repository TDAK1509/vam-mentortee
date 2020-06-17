<?php
class VamMentorAdmin {
  public function init() {
    // Include css file
    add_action('show_user_profile', [$this, 'importCss']);

    // Add extra fields to user profile form
    add_action( 'show_user_profile', [$this, 'addExtraProfileFields'] );

    // Update extra fields on user update
    add_action('personal_options_update', [$this, 'updateExtraProfileFields']);

    // Add user role
    add_action('init', [$this, 'addUserRole']);    
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
    echo '
    <h2>Mentor profile</h2>
    <table class="form-table" role="presentation">
      <tbody>
        ' . $this->getRadioFieldHTML("Gender", "gender", $this->getGenderData()) . '
        ' . $this->getTextFieldHTML("Company", "company", $this->getTextFieldData("company")) . '
        ' . $this->getTextFieldHTML("Title", "title", $this->getTextFieldData("title")) . '
        ' . $this->getTextFieldHTML("Phone", "phone", $this->getTextFieldData("phone")) . '
        ' . $this->getTextFieldHTML("Method of contact", "method_of_contact", $this->getTextFieldData("method_of_contact")) . '
        ' . $this->getTextFieldHTML("Meeting frequency", "meeting_frequency", $this->getTextFieldData("meeting_frequency")) . '
        ' . $this->getTextFieldHTML("Year of experience", "year_of_experience", $this->getTextFieldData("year_of_experience")) . '
        ' . $this->getRadioFieldHTML("Degree", "degree", $this->getDegreeData()) . '
        ' . $this->getTextFieldHTML("Current career field(s)", "current_career", $this->getTextFieldData("current_career")) . '
        ' . $this->getTextFieldHTML("Current job function(s)", "current_job", $this->getTextFieldData("current_job")) . '
        ' . $this->getTextFieldHTML("Past career field(s)", "past_career", $this->getTextFieldData("past_career")) . '
        ' . $this->getTextFieldHTML("Past job function(s)", "past_job", $this->getTextFieldData("past_job")) . '
        ' . $this->getTextFieldHTML("Specific topics you feel comfortable advising about", "topics", $this->getTextFieldData("topics")) . '
        ' . $this->getTextFieldHTML("Describe the activities, interests, and/or hobbies that are most meaningful to you", "hobbies", $this->getTextFieldData("hobbies")) . '
        ' . $this->getTextFieldHTML("Mentee capacity", "mentee_capacity", $this->getTextFieldData("mentee_capacity")) . '
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

  private function getGenderData() {
    $gender = get_user_meta(get_current_user_id(), 'gender', true);
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
    $degree = get_user_meta(get_current_user_id(), 'degree', true);
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

  private function getTextFieldData($fieldName) {
    return get_user_meta(get_current_user_id(), $fieldName, true);
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
    update_user_meta($userId, 'meeting_frequency', $_REQUEST['meeting_frequency']);
    update_user_meta($userId, 'year_of_experience', $_REQUEST['year_of_experience']);
    update_user_meta($userId, 'current_career', $_REQUEST['current_career']);
    update_user_meta($userId, 'current_job', $_REQUEST['current_job']);
    update_user_meta($userId, 'past_career', $_REQUEST['past_career']);
    update_user_meta($userId, 'past_job', $_REQUEST['past_job']);
    update_user_meta($userId, 'topics', $_REQUEST['topics']);
    update_user_meta($userId, 'hobbies', $_REQUEST['hobbies']);
    update_user_meta($userId, 'mentee_capacity', $_REQUEST['mentee_capacity']);
  }
}