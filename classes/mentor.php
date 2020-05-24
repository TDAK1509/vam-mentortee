<?php
class VamMentor {
  public function init() {
    // Add extra fields to user profile form
    add_action( 'show_user_profile', [$this, 'addExtraProfileFields'] );

    // Update extra fields on user update
    add_action('personal_options_update', [$this, 'updateExtraProfileFields']);

    // Add user role
    add_action('init', [$this, 'addUserRole']);
    add_shortcode( 'vammentor', [$this, 'getTemplate'] );
  }

  public function addUserRole() {
    $capabilities = [
      'read' => true
    ];
    remove_role('mentor');
    add_role('mentor', 'Mentor', $capabilities);
  }

  public function getTemplate() {
    $args1 = [
      'role' => 'mentor',
      'orderby' => 'user_registered',
      'order' => 'ASC'
    ];
    
    $mentors = get_users($args1);
    $a = '<ul>';
    foreach ($mentors as $user) {
      $a .= "<img src='" . get_avatar_url($user->ID) . "' />";
      $a .= "<p>$user->display_name</p>";
    }
    $a .= '</ul>';
    return $a;
    // require_once DIR_PLUGIN . 'templates/mentor.php';
  }

  public function addExtraProfileFields(WP_User $user) {
    echo '
    <h2>Mentor profile</h2>
    <table class="form-table" role="presentation">
      <tbody>
        ' . $this->getRadioFieldHTML("Gender", "gender", $this->getGenderData()) . '
        ' . $this->getTextFieldHTML("Company", "company", $this->getCompanyData()) . '
      </tbody>
    </table>';
  }

  private function getRadioFieldHTML($label, $name, $radios) {
    $radiosHTML = "";

    foreach($radios as $radio) {
      $id = $radio['id'];
      $value = $radio['value'];
      $radioLabel = $radio['label'];
      $checked = $radio['checked'] ? 'checked' : '';

      $radiosHTML .= "
        <label for='$id'>
          <input name='$name' id='$id' type='radio' value='$value' $checked />
          $radioLabel
        </label>
      ";
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

    $data = [
      [
        "id" => "male",
        "value" => "male",
        "label" => "Male",
        "checked" => $gender === "male"
      ],

      [
        "id" => "female",
        "value" => "female",
        "label" => "Female",
        "checked" => $gender === "female"
      ],

      [
        "id" => "others",
        "value" => "others",
        "label" => "Others",
        "checked" => $gender === "others"
      ]
    ];

    return $data;
  }

  private function getCompanyData() {
    $company = get_user_meta(get_current_user_id(), 'company', true);
    return $company;
  }

  public function updateExtraProfileFields($userId) {
    if (!current_user_can('edit_user', $userId)) {
      return;
    }

    update_user_meta($userId, 'gender', $_REQUEST['gender']);
    update_user_meta($userId, 'company', $_REQUEST['company']);
  }
}