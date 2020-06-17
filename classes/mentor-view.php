<?php
if (!defined('ABSPATH')) {
    die;
}

class VamMentorView {
    public static function init() {
        $self = new self();
        // Include css file
        add_action('wp', [$self, 'importCss']);

        add_shortcode( 'vammentor', [$self, 'getTemplate'] );
    }

    public function importCss() {
        wp_enqueue_style('vammentor-view', DIR_PLUGIN . "/css/mentor_view.css");
    }

    public function getTemplate() {        
        $mentors = $this->getMentors();

        $html = "
        <section class='mentor-view'>
            <h2 class='mentor-view__heading elementor-heading-title elementor-size-default'>DANH SÁCH MENTOR</h2>
            <div class='mentor-view__filter-container'>
                <h4 class='mentor-view__filter-description'>Lọc theo:</h4>

                <div class='mentor-view__select-container'>
                    <select class='mentor-view__select'>
                        <option value=''>Chương trình mentoring</option>
                        <option>B</option>
                        <option>C</option>
                    </select>
                </div>

                <div class='mentor-view__select-container'>
                    <select class='mentor-view__select'>
                        <option value=''>Lĩnh vực chia sẻ</option>
                        <option>B</option>
                        <option>C</option>
                    </select>
                </div>
            </div>
        ";

        $html .= "<div class='mentor-view__mentors'><ul class='mentor-view__list'>";
        foreach ($mentors as $user) {
            $html .= "
            <li class='mentor-view__list-item'>
                <img src='" . get_avatar_url($user->ID) . "' />
                <p>$user->display_name</p>
            </li>";
        }
        $html .= '</ul></div></section>';
        return $html;
    }

    private function getMentors() {
        return get_users([
            'role' => 'mentor',
            'orderby' => 'user_registered',
            'order' => 'ASC'
        ]);
    }
}