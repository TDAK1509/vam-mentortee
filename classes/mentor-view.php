<?php
if (!defined('ABSPATH')) {
    die;
}

class VamMentorView {
    public static function init() {
        $self = new self();
        add_shortcode( 'vammentor', [$self, 'getTemplate'] );
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
    }
}