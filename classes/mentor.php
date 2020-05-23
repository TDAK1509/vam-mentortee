<?php
class VamMentor {
  public function init() {
    // Add extra fields to user profile form
    add_action( 'show_user_profile', [$this, 'addExtraProfileFields'] );

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
      </tbody>
    </table>';
  }

  private function getRadioFieldHTML($label, $name, $radios) {
    $radiosHTML = "";

    foreach($radios as $radio) {
      $id = $radio['id'];
      $value = $radio['value'];
      $radioLabel = $radio['label'];

      $radiosHTML .= "
        <label for='$id'>
          <input name='$name' id='$id' type='radio' value='$value' />
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

  private function getGenderData() {
    return [
      [
        "id" => "male",
        "value" => "male",
        "label" => "Male"
      ],

      [
        "id" => "female",
        "value" => "female",
        "label" => "Female"
      ],

      [
        "id" => "others",
        "value" => "others",
        "label" => "Others"
      ]
    ];
  }
}